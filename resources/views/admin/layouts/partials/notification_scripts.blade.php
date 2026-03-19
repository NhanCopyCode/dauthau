<script src="https://www.gstatic.com/firebasejs/5.7.1/firebase.js"></script>
<script>
{{--    navigator.serviceWorker.register('{{ asset('firebase-messaging-sw.js') }}');--}}
    // Initialize Firebase
    var config = {
        apiKey: "{{ env('FIREBASE_API_KEY') }}",
        authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
        databaseURL: "{{ env('FIREBASE_DATABASE_URL') }}",
        projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
        storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSEGING_SENDER_ID') }}"
    };

    const isSupport = ("Notification" in window) && ('serviceWorker' in navigator) && ("PushManager" in window);
    if (isSupport) {
        firebase.initializeApp(config);
        const messaging = firebase.messaging();
        // Add the public key generated from the console here.
        messaging.usePublicVapidKey("{{ env('FIREBASE_FCM_PUBLIC_KEY') }}");

        // [START refresh_token]
        // Callback fired if Instance ID token is updated.
        messaging.onTokenRefresh(function() {
            messaging.getToken().then(function(refreshedToken) {
                // Indicate that the new Instance ID token has not yet been sent to the
                // app server.
                setTokenSentToServer(false);
                // Send Instance ID token to app server.
                sendTokenToServer(refreshedToken);
                // [START_EXCLUDE]
                // Display new Instance ID token and clear UI of all previous messages.
                resetUI();
                // [END_EXCLUDE]
            }).catch(function(err) {
                showToken('Unable to retrieve refreshed token ', err);
            });
        });
        // [END refresh_token]
        // [START receive_message]
        // Handle incoming messages. Called when:
        // - a message is received while the app has focus
        // - the user clicks on an app notification created by a service worker
        //   `messaging.setBackgroundMessageHandler` handler.
        messaging.onMessage(function(payload) {
            @if(env('APP_DEBUG'))
console.log('Message received. ', payload);
            @endif
axios.get('{{ url('notifications/number-unread') }}')
                .then(function (res) {
                    $('#notify_number').text(res.data);
                });
        });
        // [END receive_message]
        function resetUI() {
            @if(env('APP_DEBUG'))
showToken('loading...');
            @endif
            // [START get_token]
            // Get Instance ID token. Initially this makes a network call, once retrieved
            // subsequent calls to getToken will return from cache.
            messaging.getToken().then(function(currentToken) {
                if (currentToken) {
                    sendTokenToServer(currentToken);
                    showToken(currentToken);
                } else {
                    @if(env('APP_DEBUG'))
                    // Show permission request.
                    console.log('No Instance ID token available. Request permission to generate one.');
                    @endif
setTokenSentToServer(false);
                }
            }).catch(function(err) {
                showToken('Error retrieving Instance ID token. ', err);
                setTokenSentToServer(false);
            });
            // [END get_token]
        }
        function showToken(currentToken) {
            @if(env('APP_DEBUG'))
console.log('currentToken', currentToken);
            @endif
        }
        // Send the Instance ID token your application server, so that it can:
        // - send messages back to this app
        // - subscribe/unsubscribe the token from topics
        function sendTokenToServer(currentToken) {
            if (!isTokenSentToServer()) {
                @if(env('APP_DEBUG'))
console.log('Sending token to server...');
                @endif

axios.post("{{ url('fcm-token/store') }}", {
                    token: currentToken
                });
                setTokenSentToServer(true);
            } else {
                @if(env('APP_DEBUG'))
console.log('Token already sent to server so won\'t send it again ' +
                    'unless it changes');
                @endif
            }
        }
        function isTokenSentToServer() {
            return window.localStorage.getItem('sentToServer') === '{{ auth()->user()->id }}';
        }
        function setTokenSentToServer(sent) {
            window.localStorage.setItem('sentToServer', sent ? '{{ auth()->user()->id }}' : '0');
        }
        function requestPermission() {
            @if(env('APP_DEBUG'))
console.log('Requesting permission...');
            @endif
            // [START request_permission]
            messaging.requestPermission().then(function() {
                @if(env('APP_DEBUG'))
console.log('Notification permission granted.');
                @endif
                // [START_EXCLUDE]
                // In many cases once an app has been granted notification permission, it
                // should update its UI reflecting this.
                resetUI();
                // [END_EXCLUDE]
            }).catch(function(err) {
                @if(env('APP_DEBUG'))
console.log('Unable to get permission to notify.', err);
                @endif
            });
            // [END request_permission]
        }
        function deleteToken() {
            // Delete Instance ID token.
            // [START delete_token]
            messaging.getToken().then(function(currentToken) {
                Promise.all([messaging.deleteToken(currentToken), axios.delete("{{ url('fcm-token/delete') }}" + '/' + currentToken)]).then(function() {
                    @if(env('APP_DEBUG'))
console.log('Token deleted.');
                    @endif
setTokenSentToServer(false);

                    document.getElementById('logout-form').submit();
                    // [START_EXCLUDE]
                    // Once token is deleted update UI.
                    //resetUI();
                    // [END_EXCLUDE]
                }).catch(function(err) {
                    @if(env('APP_DEBUG'))
console.log('Unable to delete token. ', err);
                    @endif
document.getElementById('logout-form').submit();
                });
                // [END delete_token]
            }).catch(function(err) {
                showToken('Error retrieving Instance ID token. ', err);
                document.getElementById('logout-form').submit();
            });
        }
        requestPermission();
    }
    $('#logout').click(function (event) {
        console.log('logout');
        event.preventDefault();
        document.getElementById('logout-form').submit();
        // if (isSupport) {
        //     deleteToken();
        // }else{
        //     document.getElementById('logout-form').submit();
        // }
    });
    //resetUI();
    /*
     navigator.serviceWorker.register('{{ asset('/firebase-messaging-sw.js') }}', {scope: '/firebase-cloud-messaging-push-scope'}).then(function(registration) {
     registration.unregister().then(function(boolean) {});
     }).catch(function(error) {
     });
     */
    $(function(){
        $('.notifications-menu>a').click(function () {
            console.log('click notify btn');
            const _this = $(this);
            _this.parent().find('.menu').html('<li><a href="#">{{ __('message.loading') }}</a></li>');

            axios({
                method:'get',
                url:'{{ url('notifications?ajax=true') }}',
                responseType:'text'
            })
                .then(function(res) {
                    console.log(res);
                    _this.parent().find('.menu').replaceWith(res.data);
                    _this.parent().find('.menu').css({'max-height': '400px'});
                    $('#notify_number').text(0);
                });
        });
    });
</script>