function initializePusher(apiKey, cluster, callback) {
    // إنشاء مثيل Pusher باستخدام apiKey والـ cluster المقدمين
    var pusher = new Pusher(apiKey, {
        cluster: cluster
    });

    // الاشتراك في القناة المحددة
    var channel = pusher.subscribe('my-channel');

    // ربط الحدث بالقناة مع استخدام callback للتعامل مع البيانات
    channel.bind('my-event', function (data) {
        // استخدم callback لتعامل مع البيانات بدلاً من alert
        callback(data);
    });
}