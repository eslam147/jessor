<li class="{{ $loop->first ? 'selected' : '' }}"
    data-date="{{ now()->subMonths($index)->format('d/m/Y') }}">
    <div class="col-md-7">
    <div class="card">
            <div class="box overflow-hidden">
                @isset($question['image'])
                    <figure class="img-hov-zoomin mb-0">
                        <img class="ask_img" src="{{ $question['image'] }}"
                            alt="{{ $question['question'] }}">
                    </figure>
                @endisset
                <div class="box-body pt-0 px-0">
                    <h5
                        class="text-justify bg-{{ collect(['primary', 'success', 'danger', 'warning', 'info', 'dark'])->random() }} p-5 px-25 text-break text-white text-wrap">
                        {{ $loop->iteration }}. {!! $question['question'] !!}
                    </h5>
                    <hr>
                    <div class="align-items-center mt-3 px-15">
                        <input type="hidden"
                            name="answers_data[{{ $question['id'] }}][question_id]"
                            value="{{ $question['id'] }}">
                        @foreach ($question['options'] as $option)
                            <div class="form-check form-quiz">
                                <input @checked(old("answers_data.{$question['id']}.option_id") == $option['id'])
                                    class="form-check-input answer_qs"
                                    type="radio"
                                    name="answers_data[{{ $question['id'] }}][option_id][]"
                                    id="question{{ $question['id'] }}_{{ $option['id'] }}"
                                    value="{{ $option['id'] }}" required>
                                <label class="form-check-label text-justify"
                                    for="question{{ $question['id'] }}_{{ $option['id'] }}">
                                    {!! $option['option'] !!}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @if (!empty($question['note']))
                <div class="bg-light card-footer ">
                    <b>Note</b>: <p class="text-justify">
                        {{ $question['note'] }}</p>
                </div>
            @endif
        </div>
    </div>
</li>