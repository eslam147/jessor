<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Enums\Response\HttpResponseCode;
use Illuminate\Support\Facades\Validator;
use App\Factories\MeetingProvider\MeetingProviderFactory;

class MeetingProviderController extends Controller
{
    public function __construct(
        private readonly MeetingProviderFactory $meetingProviderFactory
    ) {
        $this->middleware('permission:meeting-provider-settings', ['only' => ['show']]);
        $this->middleware('permission:meeting-provider-settings-update', ['only' => ['update']]);
    }
    public function show($serviceName)
    {
        $videoServices = collect(config('meetings.providers'));
        $service = $videoServices->where('name', $serviceName)->where('is_active', true)->firstOrFail();

        $serviceSettings = $this->meetingProviderFactory->serviceSettings($serviceName);
        $serviceBuilder = $this->meetingProviderFactory
            ->setProvider($service['name'])
            ->build();

        $tutorial = $serviceBuilder->tutorial();

        foreach ($service['fields'] as $field => $values) {
            $serviceSettings[$field] = ! empty($serviceSettings[$field]) ? $serviceSettings[$field] : '---';
        }

        return view('meeting_provider.settings.show', compact('service', 'serviceSettings', 'tutorial'));
    }
    public function update(Request $request, string $serviceName)
    {
        try {
            $videoServices = collect(config('meetings.providers'));
            $service = $videoServices->where('name', $serviceName)->where('is_active', true)->firstOrFail();
            $rules = [];
            foreach ($service['fields'] as $field => $values) {
                if ($values['required']) {
                    $rules[$field] = 'required';
                }
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first()
                ]);
            }
            $serviceSettings = $this->meetingProviderFactory->serviceSettings($serviceName);
            foreach ($service['fields'] as $field => $values) {
                $serviceSettings[$field] = $request->input($field);
            }
            $serviceSettings['is_enabled'] = $request->boolean('is_enabled');
            $checkIsValid = $this->checkCredentials(
                $serviceName,
                [
                    'client_id' => $request->input('client_id'),
                    'client_secret' => $request->input('client_secret'),
                    'account_id' => $request->input('account_id'),
                ]
            );

            if (! $checkIsValid) {
                return response()->json([
                    'error' => true,
                    'message' => trans("meeting.errors.invalid_credentials")
                ]);
            }

            Settings::updateOrCreate([
                'type' => "video_conference_" . strtolower($serviceName),
            ], [
                'message' => json_encode($serviceSettings),
            ]);

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ], HttpResponseCode::SUCCESS);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => []
            ], HttpResponseCode::INTERNAL_SERVER_ERROR);
        }
    }
    private function checkCredentials(string $service, $credentials = []): bool
    {
        try {
            $serviceFactory = app(MeetingProviderFactory::class)
                ->setProvider($service)
                ->setCredentials($credentials)
                ->getProviderInstance();

            return $serviceFactory->credentialsIsValid();
        } catch (\Exception $e) {
            report($e);
        }
        return false;
    }
}