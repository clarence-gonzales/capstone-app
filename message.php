<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <title>BandMate - Messages</title>
  <style>
    :root {
      --primary-color: #4361ee;
      --primary-dark: #3a56d4;
      --text-color: #2b2d42;
      --light-gray: #f8f9fa;
      --medium-gray: #e9ecef;
      --dark-gray: #6c757d;
      --dark-bg: #1e1e2d;
      --card-bg: #2a2a3a;
      --hover-bg: #3a3a4a;
      --border-color: #3a3a4a;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: var(--text-color);
      line-height: 1.6;
    }

    /* Main container */
    .messaging-container {
      display: flex;
      max-width: 1200px;
      margin: 20px auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      height: calc(100vh - 100px);
    }

    /* Sidebar */
    .conversations-sidebar {
      width: 350px;
      border-right: 1px solid var(--medium-gray);
      background: white;
      display: flex;
      flex-direction: column;
    }

    .sidebar-header {
      padding: 20px;
      border-bottom: 1px solid var(--medium-gray);
    }

    .sidebar-header h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar-header h2 i {
      color: var(--primary-color);
    }

    /* Search bar */
    .search-container {
      padding: 15px;
      border-bottom: 1px solid var(--medium-gray);
    }

    .search-box {
      position: relative;
    }

    .search-box input {
      width: 100%;
      padding: 10px 15px 10px 40px;
      border: 1px solid var(--medium-gray);
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .search-box input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--dark-gray);
    }

    /* Conversation list */
    .conversation-list {
      flex: 1;
      overflow-y: auto;
      padding: 5px 0;
    }

    .conversation-item {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      cursor: pointer;
      transition: background-color 0.2s;
      border-bottom: 1px solid var(--medium-gray);
    }

    .conversation-item:hover {
      background-color: var(--light-gray);
    }

    .conversation-item.active {
      background-color: #f0f4ff;
    }

    .conversation-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .conversation-details {
      flex: 1;
      min-width: 0;
    }

    .conversation-name {
      font-weight: 500;
      margin-bottom: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .conversation-preview {
      font-size: 0.85rem;
      color: var(--dark-gray);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .conversation-meta {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      margin-left: 10px;
    }

    .conversation-time {
      font-size: 0.75rem;
      color: var(--dark-gray);
      margin-bottom: 5px;
    }

    .conversation-badge {
      background-color: var(--primary-color);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      font-weight: 600;
    }

    /* Main chat area */
    .chat-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: white;
    }

    .chat-header {
      padding: 20px;
      border-bottom: 1px solid var(--medium-gray);
      display: flex;
      align-items: center;
    }

    .chat-header-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .chat-header-info {
      flex: 1;
    }

    .chat-header-name {
      font-weight: 600;
      margin-bottom: 3px;
    }

    .chat-header-status {
      font-size: 0.85rem;
      color: var(--dark-gray);
    }

    .chat-header-actions {
      display: flex;
      gap: 15px;
    }

    .chat-header-actions i {
      color: var(--dark-gray);
      cursor: pointer;
      font-size: 1.1rem;
      transition: color 0.2s;
    }

    .chat-header-actions i:hover {
      color: var(--primary-color);
    }

    /* Messages container */
    .messages-container {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background-color: #f9f9f9;
      background-image:
        linear-gradient(#efefef 1px, transparent 1px),
        linear-gradient(90deg, #efefef 1px, transparent 1px);
      background-size: 20px 20px;
      display: flex; /* Make it a flex container */
      flex-direction: column; /* Stack messages vertically */
    }

    .message {
      display: flex;
      margin-bottom: 15px;
      max-width: 70%;
    }

    .message.incoming {
      align-self: flex-start;
    }

    .message.outgoing {
      align-self: flex-end;
      flex-direction: row-reverse;
    }

    .message-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 10px;
    }

    .message-content {
      padding: 12px 16px;
      border-radius: 18px;
      font-size: 0.95rem;
      line-height: 1.4;
      position: relative;
      word-wrap: break-word;
    }

    .message.incoming .message-content {
      background: white;
      color: var(--text-color);
      border-radius: 0 18px 18px 18px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .message.outgoing .message-content {
      background: var(--primary-color);
      color: white;
      border-radius: 18px 0 18px 18px;
    }

    .message-time {
      font-size: 0.7rem;
      color: var(--dark-gray);
      margin-top: 5px;
      text-align: right;
    }

    .message.outgoing .message-time {
      color: rgba(255, 255, 255, 0.7);
    }

    /* Message input */
    .message-input-container {
      padding: 15px 20px;
      border-top: 1px solid var(--medium-gray);
      background: white;
    }

    .message-input-box {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .message-input {
      flex: 1;
      padding: 12px 15px;
      border: 1px solid var(--medium-gray);
      border-radius: 25px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      resize: none;
      max-height: 120px;
    }

    .message-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .send-button {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: var(--primary-color);
      color: white;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .send-button:hover {
      background: var(--primary-dark);
    }

    /* Dropdown menu */
    .dropdown-menu {
      position: absolute;
      right: 10px;
      top: 60px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      padding: 8px 0;
      min-width: 180px;
      z-index: 100;
      display: none;
    }

    .dropdown-menu.show {
      display: block;
    }

    .dropdown-item {
      padding: 10px 15px;
      cursor: pointer;
      font-size: 0.9rem;
      color: var(--text-color);
      transition: background-color 0.2s;
    }

    .dropdown-item:hover {
      background: var(--light-gray);
    }

    .dropdown-item.danger {
      color: #e63946;
    }

    /* Empty state */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
      padding: 40px;
      color: var(--dark-gray);
    }

    .empty-state i {
      font-size: 3rem;
      color: var(--medium-gray);
      margin-bottom: 20px;
    }

    .empty-state h4 {
      font-size: 1.2rem;
      color: var(--dark-gray);
      margin-bottom: 10px;
    }

    .empty-state p {
      color: var(--dark-gray);
      font-size: 0.9rem;
      max-width: 300px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .messaging-container {
        flex-direction: column;
        height: auto;
      }

      .conversations-sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid var(--medium-gray);
      }

      .chat-area {
        height: 60vh;
      }
    }
  </style>
</head>
<body>
  <?php include_once "component/navs.php"; ?>

  <div class="messaging-container">
    <div class="conversations-sidebar">
      <div class="sidebar-header">
        <h2><i class="fas fa-message"></i> Messages</h2>
      </div>

      <div class="search-container">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" id="conversation-search" placeholder="Search conversations...">
        </div>
      </div>

      <div class="conversation-list">
        <div class="empty-state" id="conversations-empty-state">
          <i class="fas fa-comment-dots"></i>
          <h4>Loading conversations...</h4>
          <p>Please wait while we fetch your chats.</p>
        </div>
      </div>
    </div>

    <div class="chat-area">
      <div class="chat-header">
        <img src="images/default.jpg" alt="Profile" class="chat-header-avatar" id="chat-header-avatar">
        <div class="chat-header-info">
          <div class="chat-header-name" id="chat-header-name">Select a conversation</div>
          <div class="chat-header-status" id="chat-header-status"></div>
        </div>
        <div class="chat-header-actions">
          <i class="fas fa-phone"></i>
          <i class="fas fa-video"></i>
          <i class="fas fa-ellipsis-v" id="chat-menu-toggle"></i>
        </div>

        <div class="dropdown-menu" id="chat-dropdown">
          <div class="dropdown-item">Mark as unread</div>
          <div class="dropdown-item">Mute notifications</div>
          <div class="dropdown-item danger">Delete conversation</div>
        </div>
      </div>

      <div class="messages-container" id="messages-container">
        <div class="empty-state" id="messages-empty-state">
          <i class="fas fa-comments"></i>
          <h4>No conversation selected</h4>
          <p>Select a conversation from the sidebar to view messages.</p>
        </div>
        </div>

      <div class="message-input-container">
        <div class="message-input-box">
          <textarea class="message-input" id="message-input" placeholder="Type a message..." disabled></textarea>
          <button class="send-button" id="send-button" disabled>
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // --- Global Variables ---
    const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>; // Get current user ID from PHP session
    let selectedConversationId = null;
    let selectedConversationOtherUser = null; // Store information about the other user in the selected conversation

    // --- DOM Elements ---
    const conversationListDiv = document.querySelector('.conversation-list');
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const chatHeaderName = document.getElementById('chat-header-name');
    const chatHeaderStatus = document.getElementById('chat-header-status');
    const chatHeaderAvatar = document.getElementById('chat-header-avatar');
    const conversationsEmptyState = document.getElementById('conversations-empty-state');
    const messagesEmptyState = document.getElementById('messages-empty-state');
    const conversationSearchInput = document.getElementById('conversation-search');

    // --- Helper Functions ---
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function formatDate(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleDateString();
    }

    function showEmptyState(element, show) {
        element.style.display = show ? 'flex' : 'none';
    }

    function enableMessageInput(enable) {
        messageInput.disabled = !enable;
        sendButton.disabled = !enable;
    }

    function scrollToBottom(element) {
        element.scrollTop = element.scrollHeight;
    }

    // --- Fetch and Render Conversations ---
    async function fetchConversations() {
        showEmptyState(conversationsEmptyState, true);
        try {
            const response = await fetch('get_conversations.php');
            const conversations = await response.json();

            conversationListDiv.innerHTML = ''; // Clear existing list

            if (conversations.length === 0) {
                conversationListDiv.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-comment-dots"></i>
                        <h4>No conversations yet</h4>
                        <p>Start a new conversation by searching for users or connecting with bandmates.</p>
                    </div>
                `;
            } else {
                conversations.forEach(card => {
                    const conversationItem = document.createElement('div');
                    conversationItem.classList.add('conversation-item');
                    conversationItem.setAttribute('data-conversation-id', card.conversation_id);
                    conversationItem.setAttribute('data-other-user-id', card.other_user_id);
                    conversationItem.setAttribute('data-other-user-name', `${card.firstname} ${card.lastname}`);
                    conversationItem.setAttribute('data-other-user-avatar', card.profilePicture || 'images/default.jpg'); // Default image if none

                    let lastMessagePreview = card.last_message_preview ? card.last_message_preview : 'No messages yet.';
                    if (lastMessagePreview.length > 30) {
                        lastMessagePreview = lastMessagePreview.substring(0, 27) + '...';
                    }

                    const unreadBadge = card.unread_count > 0 ? `<div class="conversation-badge">${card.unread_count}</div>` : '';
                    const lastMessageTime = card.last_message_time ? formatTime(card.last_message_time) : '';


                    conversationItem.innerHTML = `
                        <img src="${htmlspecialchars(card.profilePicture || 'images/default.jpg')}" alt="Profile" class="conversation-avatar">
                        <div class="conversation-details">
                            <div class="conversation-name">${htmlspecialchars(card.firstname + ' ' + card.lastname)}</div>
                            <div class="conversation-preview">${htmlspecialchars(lastMessagePreview)}</div>
                        </div>
                        <div class="conversation-meta">
                            <div class="conversation-time">${lastMessageTime}</div>
                            ${unreadBadge}
                        </div>
                    `;
                    conversationListDiv.appendChild(conversationItem);

                    // Add click listener for each conversation item
                    conversationItem.addEventListener('click', () => selectConversation(card.conversation_id, card.firstname, card.lastname, card.profilePicture, card.other_user_id));
                });
            }
        } catch (error) {
            console.error('Error fetching conversations:', error);
            conversationListDiv.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>Failed to load conversations</h4>
                    <p>Please try again later.</p>
                </div>
            `;
        } finally {
            showEmptyState(conversationsEmptyState, false);
        }
    }

    // --- Fetch and Render Messages ---
    async function fetchMessages(conversationId) {
        showEmptyState(messagesEmptyState, true);
        messagesContainer.innerHTML = ''; // Clear previous messages
        try {
            const response = await fetch(`get_messages.php?conversation_id=${conversationId}`);
            const messages = await response.json();

            showEmptyState(messagesEmptyState, false);

            if (messages.length === 0) {
                messagesContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h4>No messages yet</h4>
                        <p>Be the first to send a message in this conversation!</p>
                    </div>
                `;
            } else {
                messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message');
                    messageDiv.classList.add(msg.sender_id == currentUserId ? 'outgoing' : 'incoming'); // Determine if incoming or outgoing

                    messageDiv.innerHTML = `
                        <img src="${htmlspecialchars(msg.profilePicture || 'images/default.jpg')}" alt="Avatar" class="message-avatar">
                        <div class="message-content">
                            ${htmlspecialchars(msg.message_content)}
                            <div class="message-time">${formatTime(msg.timestamp)}</div>
                        </div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                });
                scrollToBottom(messagesContainer);
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
            messagesContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4>Failed to load messages</h4>
                    <p>Please try again later.</p>
                </div>
            `;
        }
    }

    // --- Select Conversation Handler ---
    function selectConversation(convId, firstname, lastname, profilePicture, otherUserId) {
        // Remove active class from all conversations
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to the clicked conversation
        const selectedItem = document.querySelector(`.conversation-item[data-conversation-id="${convId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('active');
            // Remove unread badge if present
            const unreadBadge = selectedItem.querySelector('.conversation-badge');
            if (unreadBadge) {
                unreadBadge.remove();
            }
        }

        selectedConversationId = convId;
        selectedConversationOtherUser = {
            id: otherUserId,
            name: `${firstname} ${lastname}`,
            avatar: profilePicture || 'images/default.jpg'
        };

        // Update chat header
        chatHeaderName.textContent = `${firstname} ${lastname}`;
        chatHeaderAvatar.src = profilePicture || 'images/default.jpg';
        chatHeaderStatus.textContent = 'Online'; // You'd update this dynamically with a real-time system

        enableMessageInput(true); // Enable message input and send button
        fetchMessages(selectedConversationId); // Load messages for the selected conversation
    }

    // --- Send Message Handler ---
    async function sendMessage() {
        const messageContent = messageInput.value.trim();
        if (!messageContent || !selectedConversationId) {
            return; // Don't send empty messages or if no conversation is selected
        }

        try {
            const formData = new FormData();
            formData.append('conversation_id', selectedConversationId);
            formData.append('message_content', messageContent);

            const response = await fetch('send_message.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Append the new message to the chat area immediately
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message', 'outgoing'); // It's always outgoing if sent by current user

                messageDiv.innerHTML = `
                    <img src="${htmlspecialchars(selectedConversationOtherUser.avatar)}" alt="Avatar" class="message-avatar">
                    <div class="message-content">
                        ${htmlspecialchars(messageContent)}
                        <div class="message-time">${formatTime(new Date().toISOString())}</div>
                    </div>
                `;
                messagesContainer.appendChild(messageDiv);
                scrollToBottom(messagesContainer);
                messageInput.value = ''; // Clear input field

                // Optionally, update the conversation list preview for the just sent message
                const currentConvItem = document.querySelector(`.conversation-item[data-conversation-id="${selectedConversationId}"]`);
                if (currentConvItem) {
                    const previewDiv = currentConvItem.querySelector('.conversation-preview');
                    if (previewDiv) {
                        previewDiv.textContent = messageContent.length > 30 ? messageContent.substring(0, 27) + '...' : messageContent;
                    }
                    const timeDiv = currentConvItem.querySelector('.conversation-time');
                    if (timeDiv) {
                        timeDiv.textContent = formatTime(new Date().toISOString());
                    }
                    // Move the current conversation to the top if you like
                    // conversationListDiv.prepend(currentConvItem);
                }
            } else {
                alert('Error sending message: ' + result.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('An error occurred while sending the message.');
        }
    }

    // --- Event Listeners ---
    document.getElementById('chat-menu-toggle').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('chat-dropdown').classList.toggle('show');
    });

    document.addEventListener('click', function() {
        document.getElementById('chat-dropdown').classList.remove('show');
    });

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { // Send on Enter, allow Shift+Enter for new line
            e.preventDefault();
            sendMessage();
        }
    });

    conversationSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(item => {
            const name = item.querySelector('.conversation-name').textContent.toLowerCase();
            item.style.display = name.includes(searchTerm) ? 'flex' : 'none';
        });
    });

    // Helper for HTML escaping
    function htmlspecialchars(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // --- Initial Load ---
    document.addEventListener('DOMContentLoaded', () => {
        if (currentUserId === null) {
            // Handle case where user is not logged in, redirect or show error
            conversationListDiv.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-lock"></i>
                    <h4>Not logged in</h4>
                    <p>Please log in to view your messages.</p>
                </div>
            `;
            showEmptyState(conversationsEmptyState, false); // Hide loading state
            enableMessageInput(false);
            return;
        }
        fetchConversations();
    });

  </script>
</body>
</html>