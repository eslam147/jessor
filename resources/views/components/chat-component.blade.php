<style>
    body {
        background-color: #f0f2f5;
    }

    .chat-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 30rem;
        height: 500px;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        display: none;
        flex-direction: column;
    }

    #chatView {
        height: 100%;
    }

    .chat_content {
        height: 100%;
    }

    .chat-header {
        background-color: #f8f9fa;
        padding: 10px;
        cursor: move;
    }

    .chat-messages {
        height: 75%;

        flex-grow: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .message {
        max-width: 70%;
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 15px;
    }

    .message-sent {
        background-color: #e8e5ff;
        margin-left: auto;
    }

    .message-received {
        background-color: #f0f0f0;
    }

    .chat-input {
        padding: 10px;
        background-color: #f8f9fa;
    }

    .chat-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
    }

    .chat-user:hover {
        background: #cccccc54;
        /* cursor: pointer; */
    }

    .minimized {
        height: 40px;
    }

    .chat_badge {
        transform: translate(50%, -50%);
    }

    .back_to_users {
        cursor: pointer;
    }
</style>

<button class="btn btn-primary chat-toggle btn-sm" id="chatToggle">
    <div class="position-relative">
        <i class="fa fa-commenting"></i>
    </div>
    @if ($content['total_unread_users'])
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ $content['total_unread_users'] }}
        </span>
    @endif
</button>

<div class="chat-container" id="chatContainer">
    <div class="chat-header d-flex justify-content-between align-items-center bg-primary" id="chatHeader">
        <h5 class="mb-0 d-flex">
            <div class="header_chat_info d-none">
                <span id="backToUsers" class="back_to_users">
                    <i class="fa fa-arrow-circle-o-left"></i>
                </span>
                <span class="fw-bold" id="userName"></span>
            </div>
            <div class="chat_title">
                List Student
            </div>
            <span class="text-white spinner-border spinner-border-sm mx-2 d-none" id="spinner" role="status">
            </span>
        </h5>
        <div>
            <button class="btn btn-sm btn-light" id="minimizeChat">-</button>
            <button class="btn btn-sm btn-light" id="closeChat">×</button>
        </div>
    </div>
    <div class="chat_content overflow-auto">

        <div class="chat-users" id="chatUsers">
            <div class="alert alert-warning" role="alert">
                <b>Note</b> : The Pop up Chat Feature Is Still Under Development
            </div>
            @foreach ($content['items'] as $user)
                <div class="chat-user d-flex align-items-center p-2 border-bottom ">
                    <!-- User Image -->
                    <div class="user-image col-2">
                        <img src="{{ $user['image'] }}" draggable="false" onerror="onErrorImage(event)"
                            class="img-fluid rounded-circle" alt="User Image">
                    </div>
                    <!-- User Info -->
                    <div class="user-info col-10 ps-3">
                        <div class="d-flex justify-content-between">
                            <a href="#" data-user-id="{{ $user['user_id'] }}"
                                class="open-chat text-decoration-none">
                                <span
                                    class="user-name fw-bold">{{ $user['first_name'] . ' ' . $user['last_name'] }}</span>
                            </a>
                            @if ($user['unread_message'] > 0)
                                <span class="badge badge-primary rounded-circle">{{ $user['unread_message'] }}</span>
                            @else
                                <small>{{ $user['last_message'] == null ? '' : optional($user['last_message'])->date?->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="user-details text-muted">
                            <small>
                                @if ($user['last_message'])
                                    @if ($user['last_message']->sender_id == auth()->user()->id)
                                        <small>You : </small>
                                    @endif
                                    {{ $user['last_message']->body }}
                                @endif
                            </small>
                        </div>
                    </div>
                    <!-- Action Button -->
                </div>
            @endforeach
        </div>
        <div class="chat_view d-none" id="chatView">
            <div class="chat-messages d-flex flex-column" id="chatMessages"></div>
            <div class="chat-input">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Type a message..." id="messageInput">
                    <button class="btn btn-primary" id="sendButton">Send</button>
                </div>
            </div>
        </div>
    </div>

</div>

@section('js')
    <script>
        const chatToggle = document.getElementById('chatToggle');
        const chatContainer = document.getElementById('chatContainer');
        const minimizeChat = document.getElementById('minimizeChat');
        const closeChat = document.getElementById('closeChat');
        const backToUsers = document.getElementById('backToUsers');
        const userName = $('#userName');
        // const userName = document.getElementById('userName');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const chatMessages = document.getElementById('chatMessages');
        const chatHeader = document.getElementById('chatHeader');
        let nextPage = 1;
        let currentPage = 1;
        let limit = 10;
        // let totalMessages = 0;
        let lastPage = 0;
        let currentChatUserId = null;
        $(".open-chat").click(function(e) {
            e.preventDefault();
            const userId = $(this).data('user-id');
            $("#spinner").removeClass('d-none')
            $("#chatMessages").empty();
            $.ajax({
                type: "GET",
                data: {
                    limit: limit,
                    page: nextPage
                },
                url: "{{ route('chat.messages', ':userId') }}".replace(':userId', userId),
                success: function(response) {
                    currentChatUserId = userId;

                    userName.text(response.data.user.full_name)
                    $("#chatUsers").addClass('d-none')
                    $("#chatView").removeClass('d-none')
                    $('.header_chat_info').removeClass('d-none');
                    $(".chat_title").addClass('d-none');

                    displayMessagesWithFiles(response.data.items, currentChatUserId, 'bottom', false);
                    lastPage = Math.ceil(response.data.total_items / limit);
                    nextPage = nextPage + 1;
                },
                complete: function() {
                    $("#spinner").addClass('d-none');

                }
            });
        })

        // Toggle chat visibility
        chatToggle.addEventListener('click', () => {
            chatContainer.style.display = 'flex';
            chatToggle.style.display = 'none';
        });

        // Minimize chat
        minimizeChat.addEventListener('click', () => {
            chatContainer.classList.toggle('minimized');
            minimizeChat.textContent = chatContainer.classList.contains('minimized') ? '+' : '-';
        });

        function chatReset() {
            $("#chatUsers").removeClass('d-none')
            $("#chatView").addClass('d-none')
            $('.header_chat_info').addClass('d-none');
            $(".chat_title").removeClass('d-none');
            currentChatUserId = null;
            nextPage = 1;
            currentPage = 1;
            lastPage = 0;
            $("#chatMessages").empty();
        }
        // Close chat
        backToUsers.addEventListener('click', () => {
            chatReset()
        });
        closeChat.addEventListener('click', () => {
            chatReset()
            chatContainer.style.display = 'none';
            chatToggle.style.display = 'block';
        });

        // Send message
        function sendMessage() {
            const message = messageInput.value.trim();

            if (message.length > 0) {
                addMessage({
                    body: message,
                    type: 'text',
                }, 'sent');

                // const {
                //     messageElement,
                //     updateStatus
                // } =
                // addMessage(msg, "user", {
                //     sent: false,
                //     status: 'sent',
                // });
                // addMessage({
                //     message: message,
                //     type: 'text',

                // }, "user", {
                //     sent: false,
                //     status: 'sent',
                // });

                messageInput.value = '';
                $.ajax({
                    type: "POST",
                    url: "{{ route('chat.send.message') }}",
                    data: {
                        message: message,
                        receiver_id: currentChatUserId,
                    },

                    success: function(response) {
                        // $(".chat-messages").scrollTop($("chat-messages")[0].scrollHeight);
                    },
                })
                // Simulate received message
                // setTimeout(() => {
                //     addMessage('This is a simulated response.', 'received');
                // }, 1000);
            }
        }

        function loadMoreMessages() {
            $("#spinner").removeClass('d-none')
            // const userId = $(this).data('user-id');
            //     $("#chatMessages").empty();
            $.ajax({
                type: "GET",
                data: {
                    limit: 10,
                    page: nextPage
                },
                url: "{{ route('chat.messages', ':userId') }}".replace(':userId', currentChatUserId),
                success: function(response) {
                    // currentChatUserId = userId;

                    userName.text(response.data.user.full_name)
                    $("#chatUsers").addClass('d-none')
                    $("#chatView").removeClass('d-none')
                    $('.header_chat_info').removeClass('d-none');
                    $(".chat_title").addClass('d-none');
                    if (response.data.items.length > 0) {
                        displayMessagesWithFiles(response.data.items, currentChatUserId, 'top', false);
                        currentPage = nextPage;
                        nextPage = nextPage + 1;
                    }
                },
                complete: function() {
                    $("#spinner").addClass('d-none')
                }
            });

        }
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
        chatMessages.addEventListener('scroll', function() {
            if (currentPage < lastPage) {
                var currentPosition = chatMessages.clientHeight - chatMessages.scrollTop;
                if (chatMessages.scrollHeight - 1 === currentPosition) {
                    loadMoreMessages();
                }
            }
        });

        function displayMessagesWithFiles(messages, userId, position = 'bottom', scrollTop = true) {
            Array.from(messages).forEach(msg => {
                // const {
                //     messageElement,
                //     updateStatus
                // } = 
                // addMessage(msg.body, "user", {
                //     sent: msg.sender_id === userId,
                //     status: 'sent',
                //     timeStamp: '14:30:00'
                // });
                // /
                addMessage(msg, (msg.sender_id === userId ? 'received' : 'sent'), position, scrollTop);

                if (msg.files) {
                    msg.files.forEach(file => {
                        $("#chatMessages").append(
                            `<div class="message ${msg.sender_id === userId ? 'message-sent' : 'message-received'}"> 
            <a href="${file.url}" target="_blank">${file.name}</a>
            </div>`
                        )
                    });
                }
            });
        }
        // Add message to chat
        function addMessage(message, type, position = 'bottom', scrollTop = true) {
            const messageElement = document.createElement('div');
            messageElement.className = `message message-${type}`;
            messageElement.textContent = message.body;
            const userInfo = document.createElement('div');
            userInfo.className = 'd-flex align-items-center justify-content-end';



            // <div class="d-flex align-items-center justify-content-end">
            //                 <div class="mx-10">
            //                     <a href="#" class="text-dark hover-primary fw-bold">You</a>
            //                     <p class="text-muted fs-12 mb-0">3 minutes</p>
            //                 </div>
            //                 <span class="msg-avatar">
            //                     <img src="../images/avatar/3.jpg" class="avatar avatar-lg">
            //                 </span>
            //             </div>
            if (position === 'bottom') {
                chatMessages.appendChild(messageElement);
            } else if (position === 'top') {
                chatMessages.prepend(messageElement);
            }
            if (scrollTop) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        // function addMessage(content, type, options = {}) {
        //     const {
        //         position = 'bottom',
        //             scrollTop = true,
        //             sent = true,
        //             timeStamp = 'now',
        //             status = 'sent'
        //     } = options;

        //     const messageElement = document.createElement('div');
        //     messageElement.className = `message message-${type}`;

        //     // Create and append content element
        //     const contentElement = document.createElement('div');
        //     contentElement.className = 'message-content';
        //     contentElement.textContent = content;
        //     messageElement.appendChild(contentElement);

        //     // Create and append timestamp element
        //     const timeElement = document.createElement('div');
        //     timeElement.className = 'message-time';
        //     timeElement.textContent = timeStamp;
        //     messageElement.appendChild(timeElement);

        //     // Create and append status element
        //     const statusElement = document.createElement('div');
        //     statusElement.className = 'message-status';
        //     statusElement.textContent = status;
        //     messageElement.appendChild(statusElement);

        //     if (position === 'bottom') {
        //         chatMessages.appendChild(messageElement);
        //     } else if (position === 'top') {
        //         chatMessages.prepend(messageElement);
        //     }

        //     if (scrollTop) {
        //         chatMessages.scrollTop = chatMessages.scrollHeight;
        //     }

        //     // Add retry button for failed messages
        //     if (!sent) {
        //         const retryButton = document.createElement('button');
        //         retryButton.textContent = 'Try Again';
        //         retryButton.className = 'retry-button';
        //         retryButton.onclick = () => {
        //             // Implement your resend logic here
        //             console.log('Resending message:', content);
        //         };
        //         messageElement.appendChild(retryButton);
        //     }

        //     // Update status (you can call this function later to update the status)
        //     function updateStatus(newStatus) {
        //         statusElement.textContent = newStatus;
        //     }

        //     return {
        //         messageElement,
        //         updateStatus
        //     };
        // }

        // Make chat draggable
        let isDragging = false;
        let dragOffsetX, dragOffsetY;

        chatHeader.addEventListener('mousedown', (e) => {
            isDragging = true;
            dragOffsetX = e.clientX - chatContainer.offsetLeft;
            dragOffsetY = e.clientY - chatContainer.offsetTop;
        });

        document.addEventListener('mousemove', (e) => {
            if (isDragging) {
                chatContainer.style.left = (e.clientX - dragOffsetX) + 'px';
                chatContainer.style.top = (e.clientY - dragOffsetY) + 'px';
                chatContainer.style.right = 'auto';
                chatContainer.style.bottom = 'auto';
            }
        });

        document.addEventListener('mouseup', () => {
            isDragging = false;
        });
    </script>
@endsection
