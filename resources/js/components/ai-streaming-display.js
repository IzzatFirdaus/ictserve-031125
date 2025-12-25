/**
 * AI Streaming Display Alpine.js Component
 *
 * Real-time AI streaming display component that handles streaming.started,
 * streaming.chunk, and streaming.completed events. Shows typing indicator
 * and progressive content display for AI responses.
 *
 * @trace D03 SRS-FR-008, D04 §5.3, D18 (Requirements 6.4)
 */

/**
 * Alpine.js AI Streaming Display Component
 *
 * Usage:
 * <!-- For authenticated users -->
 * <div x-data="aiStreamingDisplay({ userId: 123, conversationId: 456 })" x-init="init()">
 *   <div x-show="isStreaming" class="flex items-center space-x-2">
 *     <div class="typing-indicator">
 *       <span></span><span></span><span></span>
 *     </div>
 *     <span class="text-sm text-gray-600">AI sedang menaip...</span>
 *   </div>
 *
 *   <div x-show="hasContent" class="prose max-w-none">
 *     <div x-html="formattedContent"></div>
 *   </div>
 *
 *   <div x-show="error" class="text-red-600 text-sm" x-text="error"></div>
 * </div>
 *
 * <!-- For guest users -->
 * <div x-data="aiStreamingDisplay({ conversationUuid: 'uuid-123', statusToken: 'token-456' })" x-init="init()">
 *   <!-- Same template as above -->
 * </div>
 */
export function aiStreamingDisplay(options = {}) {
	return {
		// Component configuration
		userId: options.userId || null,
		conversationId: options.conversationId || null,
		conversationUuid: options.conversationUuid || null,
		statusToken: options.statusToken || null,

		// Component state
		isStreaming: false,
		content: "",
		chunks: [],
		error: null,
		startTime: null,
		endTime: null,
		totalTokens: 0,
		modelUsed: null,

		// Streaming state
		typingSpeed: 50, // milliseconds per character
		displayedContent: "",
		typingTimer: null,

		// Computed properties
		get hasContent() {
			return this.content.length > 0 || this.displayedContent.length > 0;
		},

		get formattedContent() {
			return this.formatMarkdown(this.displayedContent);
		},

		get channelName() {
			if (this.userId && this.conversationId) {
				return `user.${this.userId}`;
			} else if (this.conversationUuid) {
				return `conversation.${this.conversationUuid}`;
			}
			return null;
		},

		get isGuest() {
			return !this.userId && this.conversationUuid;
		},

		get streamingDuration() {
			if (!this.startTime) return 0;
			const endTime = this.endTime || new Date();
			return Math.round((endTime - this.startTime) / 1000);
		},

		// Component initialization
		init() {
			console.log("Initializing AI streaming display:", {
				userId: this.userId,
				conversationId: this.conversationId,
				conversationUuid: this.conversationUuid,
				isGuest: this.isGuest,
			});

			// Set up Echo listeners
			this.setupEchoListeners();

			// Set up custom event listeners
			this.setupCustomEventListeners();

			// Set up cleanup on component destroy
			this.setupCleanup();
		},

		// Set up Echo WebSocket listeners
		setupEchoListeners() {
			if (!window.Echo || !this.channelName) {
				console.warn("Echo not available or channel name not determined");
				return;
			}

			console.log(
				`Setting up AI streaming Echo listeners for channel: ${this.channelName}`
			);

			// Listen to appropriate channel
			const channel = window.Echo.private(this.channelName);

			channel.listen(".ai.streaming.started", (event) => {
				console.log("AI streaming started event received:", event);
				this.handleStreamingStarted(event);
			});

			channel.listen(".ai.streaming.chunk", (event) => {
				console.log("AI streaming chunk event received:", event);
				this.handleStreamingChunk(event);
			});

			channel.listen(".ai.streaming.completed", (event) => {
				console.log("AI streaming completed event received:", event);
				this.handleStreamingCompleted(event);
			});

			channel.listen(".ai.error.occurred", (event) => {
				console.log("AI error occurred event received:", event);
				this.handleAIError(event);
			});
		},

		// Set up custom event listeners
		setupCustomEventListeners() {
			// Listen for AI streaming events from echo-handlers.js or other sources
			window.addEventListener("ai:streaming:started", (event) => {
				this.handleStreamingStarted(event.detail);
			});

			window.addEventListener("ai:streaming:chunk", (event) => {
				this.handleStreamingChunk(event.detail);
			});

			window.addEventListener("ai:streaming:completed", (event) => {
				this.handleStreamingCompleted(event.detail);
			});

			window.addEventListener("ai:error:occurred", (event) => {
				this.handleAIError(event.detail);
			});

			// Listen for Echo connection events
			window.addEventListener("echo:connected", () => {
				console.log("Echo connected for AI streaming");
			});

			window.addEventListener("echo:disconnected", () => {
				console.log("Echo disconnected for AI streaming");
				this.handleConnectionLost();
			});
		},

		// Set up cleanup
		setupCleanup() {
			// Clean up typing timer when component is destroyed
			this.$watch("$el", (el) => {
				if (!el && this.typingTimer) {
					clearTimeout(this.typingTimer);
					this.typingTimer = null;
				}
			});
		},

		// Handle streaming started event
		handleStreamingStarted(event) {
			// Check if this event is for our conversation
			if (!this.isEventForThisConversation(event)) {
				return;
			}

			console.log("Starting AI streaming:", event);

			// Reset state
			this.isStreaming = true;
			this.content = "";
			this.displayedContent = "";
			this.chunks = [];
			this.error = null;
			this.startTime = new Date();
			this.endTime = null;
			this.modelUsed = event.model || null;

			// Clear any existing typing timer
			if (this.typingTimer) {
				clearTimeout(this.typingTimer);
				this.typingTimer = null;
			}

			// Dispatch custom event
			this.$dispatch("ai-streaming-started", {
				conversationId: this.conversationId,
				conversationUuid: this.conversationUuid,
				model: this.modelUsed,
			});
		},

		// Handle streaming chunk event
		handleStreamingChunk(event) {
			// Check if this event is for our conversation
			if (!this.isEventForThisConversation(event)) {
				return;
			}

			console.log("Received AI streaming chunk:", event);

			// Add chunk to collection
			this.chunks.push({
				content: event.chunk || "",
				timestamp: new Date(),
				isFinal: event.is_final || false,
			});

			// Update full content
			this.content += event.chunk || "";

			// Start typing animation for this chunk
			this.animateTyping(event.chunk || "");
		},

		// Handle streaming completed event
		handleStreamingCompleted(event) {
			// Check if this event is for our conversation
			if (!this.isEventForThisConversation(event)) {
				return;
			}

			console.log("AI streaming completed:", event);

			// Update state
			this.isStreaming = false;
			this.endTime = new Date();
			this.totalTokens = event.total_tokens || 0;

			// Ensure all content is displayed
			this.displayedContent = this.content;

			// Clear typing timer
			if (this.typingTimer) {
				clearTimeout(this.typingTimer);
				this.typingTimer = null;
			}

			// Dispatch custom event
			this.$dispatch("ai-streaming-completed", {
				conversationId: this.conversationId,
				conversationUuid: this.conversationUuid,
				totalTokens: this.totalTokens,
				duration: this.streamingDuration,
				content: this.content,
			});

			// Show completion feedback
			this.showCompletionFeedback();
		},

		// Handle AI error event
		handleAIError(event) {
			// Check if this event is for our conversation
			if (!this.isEventForThisConversation(event)) {
				return;
			}

			console.error("AI error occurred:", event);

			// Update state
			this.isStreaming = false;
			this.error = event.error || "Ralat AI berlaku";
			this.endTime = new Date();

			// Clear typing timer
			if (this.typingTimer) {
				clearTimeout(this.typingTimer);
				this.typingTimer = null;
			}

			// Dispatch custom event
			this.$dispatch("ai-error-occurred", {
				conversationId: this.conversationId,
				conversationUuid: this.conversationUuid,
				error: this.error,
				retryAvailable: event.retry_available || false,
			});
		},

		// Handle connection lost
		handleConnectionLost() {
			if (this.isStreaming) {
				this.error = "Sambungan terputus semasa streaming AI";
				this.isStreaming = false;

				if (this.typingTimer) {
					clearTimeout(this.typingTimer);
					this.typingTimer = null;
				}
			}
		},

		// Check if event is for this conversation
		isEventForThisConversation(event) {
			if (this.conversationId && event.conversation_id) {
				return event.conversation_id === this.conversationId;
			}

			if (this.conversationUuid && event.conversation_uuid) {
				return event.conversation_uuid === this.conversationUuid;
			}

			return false;
		},

		// Animate typing effect for new content
		animateTyping(newContent) {
			if (!newContent || newContent.length === 0) {
				return;
			}

			let currentIndex = 0;
			const startLength = this.displayedContent.length;

			const typeNextChar = () => {
				if (currentIndex < newContent.length) {
					this.displayedContent = this.content.substring(
						0,
						startLength + currentIndex + 1
					);
					currentIndex++;

					// Variable typing speed based on character
					const char = newContent[currentIndex - 1];
					let delay = this.typingSpeed;

					// Slower for punctuation
					if ([".", "!", "?", ";", ":"].includes(char)) {
						delay *= 3;
					} else if ([",", "-"].includes(char)) {
						delay *= 2;
					} else if (char === " ") {
						delay *= 0.5;
					}

					this.typingTimer = setTimeout(typeNextChar, delay);
				} else {
					// Finished typing this chunk
					this.typingTimer = null;
				}
			};

			// Clear existing timer
			if (this.typingTimer) {
				clearTimeout(this.typingTimer);
			}

			// Start typing animation
			typeNextChar();
		},

		// Format markdown content to HTML
		formatMarkdown(content) {
			if (!content) return "";

			// Simple markdown formatting (you might want to use a proper markdown library)
			let formatted = content
				// Bold
				.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
				// Italic
				.replace(/\*(.*?)\*/g, "<em>$1</em>")
				// Code inline
				.replace(
					/`(.*?)`/g,
					'<code class="bg-gray-100 px-1 py-0.5 rounded text-sm">$1</code>'
				)
				// Line breaks
				.replace(/\n/g, "<br>")
				// Links (basic)
				.replace(
					/\[([^\]]+)\]\(([^)]+)\)/g,
					'<a href="$2" class="text-blue-600 hover:underline" target="_blank" rel="noopener">$1</a>'
				);

			return formatted;
		},

		// Show completion feedback
		showCompletionFeedback() {
			// Add a subtle animation to indicate completion
			if (this.$el) {
				this.$el.classList.add("ai-streaming-completed");

				// Remove the class after animation
				setTimeout(() => {
					this.$el.classList.remove("ai-streaming-completed");
				}, 1000);
			}
		},

		// Clear content and reset state
		clearContent() {
			this.content = "";
			this.displayedContent = "";
			this.chunks = [];
			this.error = null;
			this.isStreaming = false;
			this.startTime = null;
			this.endTime = null;
			this.totalTokens = 0;

			if (this.typingTimer) {
				clearTimeout(this.typingTimer);
				this.typingTimer = null;
			}
		},

		// Stop streaming (if in progress)
		stopStreaming() {
			if (this.isStreaming) {
				this.isStreaming = false;
				this.endTime = new Date();

				if (this.typingTimer) {
					clearTimeout(this.typingTimer);
					this.typingTimer = null;
				}

				// Show all content immediately
				this.displayedContent = this.content;
			}
		},

		// Get streaming statistics
		getStreamingStats() {
			return {
				duration: this.streamingDuration,
				totalTokens: this.totalTokens,
				chunksReceived: this.chunks.length,
				charactersPerSecond:
					this.streamingDuration > 0
						? Math.round(this.content.length / this.streamingDuration)
						: 0,
				modelUsed: this.modelUsed,
			};
		},

		// Copy content to clipboard
		async copyContent() {
			if (!this.content) return false;

			try {
				await navigator.clipboard.writeText(this.content);

				// Show feedback
				this.$dispatch("content-copied", {
					message: "Kandungan disalin ke papan keratan",
				});

				return true;
			} catch (error) {
				console.error("Failed to copy content:", error);
				return false;
			}
		},
	};
}

// Register Alpine.js component globally
document.addEventListener("alpine:init", () => {
	window.Alpine.data("aiStreamingDisplay", aiStreamingDisplay);
});

// Export for manual registration
export default aiStreamingDisplay;
