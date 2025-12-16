# Panduan Persediaan Penyiaran & WebSockets (Broadcasting & WebSockets Setup Guide)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** RFC 6455 (WebSocket Protocol), OWASP Transport Security, Laravel Framework v12

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                |
| -------------------- | -------------------------------------------------------------------- |
| **Versi**            | 3.6.1                                                                |
| **Tarikh Kemaskini** | 17 Disember 2025                                                     |
| **Status**           | Aktif                                                                |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                           |
| **Pematuhi**         | RFC 6455, OWASP Transport Security, Laravel Framework v12            |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0)                                        |
| **Pematuhi**         | RFC 6455 (WebSocket), OWASP Transport Security, Laravel Broadcasting |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                            |

> Notis Penggunaan Dalaman: Panduan ini adalah untuk persediaan infrastruktur
> penyiaran masa nyata dalam sistem dalaman MOTAC sahaja.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                           | Penulis                 |
| ----- | ---------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Laravel Pulse (performance monitoring), Laravel Sanctum (API authentication), Google Workspace SSO (opsyen), Responsible Officer, Accessory Tracking, Form Reference Codes, MOTAC Branding. Penambahan acara penyiaran baharu untuk API token dan SSO events. Penyelarasan dengan D00-D15 v3.5.0. | Pasukan Pembangunan BPM |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), email verification, optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), notification preferences. Penyelarasan dengan D00-D14 v3.5.0. | Pasukan Pembangunan BPM |
| 1.3.0 | 29 November 2025 | Hybrid operations: Auth users (private-user.{id}) + Guests (private-ticket.{uuid})                                                                                                                                                                                                                  | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture v3.4.0: Dual channel strategy (private-user.{id} vs private-ticket.{uuid})                                                                                                                                                                                                      | Pasukan Pembangunan BPM |
| 1.2.0 | 29 November 2025 | Kemaskini saluran untuk Guest: UUID-based channels, status token authorization                                                                                                                                                                                                                      | Pasukan Pembangunan BPM |
| 1.1.0 | 29 November 2025 | Kemaskini dokumentasi; Laravel Reverb sebagai penyedia utama                                                                                                                                                                                                                                        | Pasukan Pembangunan BPM |
| 1.0.0 | 16 November 2025 | Panduan awal untuk persediaan penyiaran real-time                                                                                                                                                                                                                                                   | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Rekabentuk Teknikal
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Pengurusan Baris Gilir

---

## 1. TUJUAN PANDUAN (Purpose)

Dokumen ini merangkum persediaan infrastruktur penyiaran masa nyata (real-time
broadcasting) untuk sistem ICTServe. Penyiaran membolehkan komponen frontend
menerima pembaruan data secara langsung tanpa perlu polling, meningkatkan
pengalaman pengguna dan mengurangkan beban pelayan.

Panduan ini meliputi:

- Persediaan Laravel Reverb (penyedia utama)
- Konfigurasi persekitaran dan integrasi frontend
- Pengujian saluran peribadi (private channel authorization)
- Amalan keselamatan dan pengurusan rahasia

---

## 2. SKOP PANDUAN (Scope)

Skop merangkumi:

- Persediaan penyedia penyiaran (Reverb sebagai utama, Pusher sebagai alternatif)
- Konfigurasi persekitaran (`BROADCAST_CONNECTION`, `REVERB_*`, `VITE_*`)
- Integrasi frontend (Laravel Echo, Pusher-JS)
- Pengujian saluran peribadi (private channel authorization)
- Pengurusan pekerja baris gilir (queue worker) untuk penyiaran asinkron
- Amalan keselamatan dan pengurusan rahasia

Di luar skop:

- Konfigurasi beban seimbang produksi
- Pemantauan infrastruktur lanjutan (Prometheus, Grafana)

---

## 3. SENI BINA PENYIARAN (Broadcasting Architecture)

### 3.1. Komponen Utama

| Komponen                                     | Peranan                                       | Catatan                                  |
| -------------------------------------------- | --------------------------------------------- | ---------------------------------------- |
| **Acara Penyiaran** (Broadcasting Events)    | Kelas yang melaksanakan `ShouldBroadcast`     | Memicu penyiaran apabila dilampirkan     |
| **Penyedia Penyiaran** (Broadcast Driver)    | Menghantar mesej ke saluran                   | Reverb (utama), Pusher (alternatif)      |
| **Saluran Peribadi** (Private Channels)      | Saluran dengan kebenaran (authorization)      | Ditakrifkan dalam `routes/channels.php`  |
| **Pelanggan Frontend** (Frontend Subscriber) | Laravel Echo + Pusher-JS                      | Mendengarkan saluran peribadi di browser |
| **Pekerja Baris Gilir** (Queue Worker)       | Memproses pekerjaan penyiaran secara asinkron | `php artisan queue:work` dengan Redis    |

### 3.2. Aliran Kerja Penyiaran

```text
1. Acara dipicu (Event dispatched) → app/Events/NotificationCreated.php
   ↓
2. Penyiaran disahkan oleh shouldBroadcast() & broadcastOn()
   ↓
3. Pekerjaan baris gilir disimpan (Redis queue)
   ↓
4. Pekerja baris gilir memproses & menghubungi penyedia
   ↓
5. Penyedia menghantar mesej ke saluran (Reverb/Pusher)
   ↓
6. Frontend Echo menerima & mengemas kini antarmuka tanpa muatan semula
```

### 3.3. Acara Penyiaran Sedia Ada (True Hybrid Architecture v3.5.0)

Sistem ICTServe menggunakan **Dual Channel Strategy**:

- **Authenticated Users**: Listen to `private-user.{id}`
- **Guests**: Listen to `private-ticket.{uuid}` atau `private-loan.{uuid}`

| Acara                                 | Saluran (Auth Users)    | Saluran (Guests)                 | Peristiwa                | Catatan                                                  |
| ------------------------------------- | ----------------------- | -------------------------------- | ------------------------ | -------------------------------------------------------- |
| `App\Events\NotificationCreated`      | `private-user.{userId}` | `private-ticket.{ticketUuid}`    | `notification.created`   | Hybrid: Auth users via user_id, Guests via UUID          |
| `App\Events\StatusUpdated`            | `private-user.{userId}` | `private-ticket.{ticketUuid}`    | `status.updated`         | Hybrid: Status updates untuk authenticated DAN guests    |
| `App\Events\CommentPosted`            | `private-user.{userId}` | `private-submission.{type}.{id}` | `comment.posted`         | Ulasan baru pada tiket/pinjaman                          |
| `App\Events\AssetReturnedDamaged`     | `private-user.{userId}` | `private-loan.{loanUuid}`        | `asset.returned.damaged` | Hybrid: Loan updates untuk authenticated DAN guests      |
| `App\Events\EmailVerified`            | `private-user.{userId}` | N/A (auth only)                  | `email.verified`         | Email verification confirmed for self-registered staff   |
| `App\Events\AccountLinked`            | `private-user.{userId}` | N/A (auth only)                  | `account.linked`         | Guest submissions linked to authenticated account        |
| `App\Events\NotificationPrefsUpdated` | `private-user.{userId}` | N/A (auth only)                  | `preferences.updated`    | Notification preferences updated                         |
| `App\Events\WelcomeNotification`      | `private-user.{userId}` | N/A (auth only)                  | `welcome.notification`   | Welcome message for newly verified self-registered staff |
| `App\Events\ApiTokenCreated`          | `private-user.{userId}` | N/A (auth only)                  | `api.token.created`      | API token created via Laravel Sanctum (v3.5.0)           |
| `App\Events\ApiTokenRevoked`          | `private-user.{userId}` | N/A (auth only)                  | `api.token.revoked`      | API token revoked (v3.5.0)                               |
| `App\Events\GoogleSsoLinked`          | `private-user.{userId}` | N/A (auth only)                  | `sso.google.linked`      | Google Workspace account linked (v3.5.0)                 |
| `App\Events\AccessoryCheckedOut`      | `private-user.{userId}` | `private-loan.{loanUuid}`        | `accessory.checked.out`  | Loan accessory checked out (v3.5.0)                      |
| `App\Events\AccessoryReturned`        | `private-user.{userId}` | `private-loan.{loanUuid}`        | `accessory.returned`     | Loan accessory returned (v3.5.0)                         |
| `App\Events\ResponsibleOfficerAssigned` | `private-user.{userId}` | N/A (auth only)                | `officer.assigned`       | Responsible Officer assigned to ticket/loan (v3.5.0)     |

### 3.4. AI Real-Time Events (D18 Integration v3.6.0)

Sistem ICTServe v3.6.0 menambah sokongan untuk **Cloud Hybrid AI Architecture** dengan acara masa nyata untuk streaming responses, conversation management, dan model switching:

| Acara AI                              | Saluran (Auth Users)    | Saluran (Guests)                 | Peristiwa                | Catatan                                                  |
| ------------------------------------- | ----------------------- | -------------------------------- | ------------------------ | -------------------------------------------------------- |
| `App\Events\AiStreamingStarted`       | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.streaming.started`   | AI response streaming dimulakan                          |
| `App\Events\AiStreamingChunk`         | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.streaming.chunk`     | Chunk respons AI (Server-Sent Events)                   |
| `App\Events\AiStreamingCompleted`     | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.streaming.completed` | AI response streaming selesai                            |
| `App\Events\AiModelSwitched`          | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.model.switched`      | Model AI ditukar (Auto/Opus/Sonnet/Haiku)               |
| `App\Events\AiConversationSaved`      | `private-user.{userId}` | N/A (auth only)                  | `ai.conversation.saved`  | Perbualan AI disimpan untuk authenticated users         |
| `App\Events\AiWebSearchStarted`       | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.web.search.started`  | Web-augmented search dimulakan                          |
| `App\Events\AiWebSearchCompleted`     | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.web.search.completed`| Web search selesai, respons diperkaya                   |
| `App\Events\AiFaqSuggestionGenerated` | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.faq.suggestion`      | FAQ suggestions dijana berdasarkan pertanyaan           |
| `App\Events\AiErrorOccurred`          | `private-user.{userId}` | `private-conversation.{uuid}`    | `ai.error.occurred`      | Error dalam pemprosesan AI (fallback ke model lain)     |
| `App\Events\AiUsageMetricsUpdated`    | `private-user.{userId}` | N/A (admin only)                 | `ai.metrics.updated`     | Metrik penggunaan AI dikemas kini (admin dashboard)     |

**AI Channel Selection Logic**:

```php
// app/Events/AiStreamingChunk.php
public function broadcastOn(): array
{
    if ($this->conversation->user_id) {
        // Authenticated: Broadcast to user channel
        return [new PrivateChannel('user.' . $this->conversation->user_id)];
    } else {
        // Guest: Broadcast to conversation UUID channel
        return [new PrivateChannel('conversation.' . $this->conversation->uuid)];
    }
}

public function broadcastWith(): array
{
    return [
        'conversation_id' => $this->conversation->id,
        'chunk' => $this->chunk,
        'model_used' => $this->modelUsed,
        'is_final' => $this->isFinal,
        'timestamp' => now()->toISOString(),
    ];
}
```

**AI Streaming Implementation (Server-Sent Events)**:

```php
// app/Http/Controllers/AiStreamingController.php
public function stream(Request $request, string $conversationId)
{
    return response()->stream(function () use ($conversationId) {
        $conversation = BedrockConversation::findOrFail($conversationId);
        
        // Authorize access
        if ($conversation->user_id && $conversation->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Start streaming
        event(new AiStreamingStarted($conversation));
        
        // Process with selected model
        $service = app(BedrockService::class);
        $chunks = $service->streamResponse($conversation);
        
        foreach ($chunks as $chunk) {
            // Broadcast each chunk
            event(new AiStreamingChunk($conversation, $chunk));
            
            // Send SSE
            echo "data: " . json_encode([
                'content' => $chunk,
                'conversation_id' => $conversationId
            ]) . "\n\n";
            
            ob_flush();
            flush();
        }
        
        // Complete streaming
        event(new AiStreamingCompleted($conversation));
        
        echo "event: complete\n";
        echo "data: {\"status\": \"completed\"}\n\n";
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
}
```

**Channel Selection Logic**:

```php
public function broadcastOn(): array
{
    if ($this->ticket->user_id) {
        // Authenticated: Broadcast to user channel
        return [new PrivateChannel('user.' . $this->ticket->user_id)];
    } else {
        // Guest: Broadcast to ticket UUID channel
        return [new PrivateChannel('ticket.' . $this->ticket->uuid)];
    }
}
```

**Dual Channel Strategy (Frontend)**:

```javascript
// Authenticated Users: Listen to private-user.{id}
if (window.userId) {
 window.Echo.private(`user.${window.userId}`)
  .listen(".notification.created", (data) => {
   // Update notification bell count
   // Show toast notification
  })
  .listen(".status.updated", (data) => {
   // Update submission status in UI
  });
}

// Guests: Listen to private-ticket.{uuid} (with status token)
if (window.ticketUuid && window.statusToken) {
 window.Echo.private(`ticket.${window.ticketUuid}`)
  .listen(".notification.created", (data) => {
   // Show toast notification
  })
  .listen(".status.updated", (data) => {
   // Update ticket status display
  });
}

// Guests: Listen to private-loan.{uuid} (with status token)
if (window.loanUuid && window.statusToken) {
 window.Echo.private(`loan.${window.loanUuid}`)
  .listen(".notification.created", (data) => {
   // Show toast notification
  })
  .listen(".asset.returned.damaged", (data) => {
   // Update loan status display
  })
  .listen(".accessory.checked.out", (data) => {
   // Update accessory status (v3.5.0)
  })
  .listen(".accessory.returned", (data) => {
   // Update accessory return status (v3.5.0)
  });
}

// v3.5.0: Listen for API token events (authenticated users only)
if (window.userId) {
 window.Echo.private(`user.${window.userId}`)
  .listen(".api.token.created", (data) => {
   // Show API token created notification
  })
  .listen(".api.token.revoked", (data) => {
   // Show API token revoked notification
  })
  .listen(".sso.google.linked", (data) => {
   // Show Google SSO linked notification
  });
}

// v3.6.0: AI Real-Time Events (D18 Integration)
// Authenticated Users: AI conversation events
if (window.userId) {
 window.Echo.private(`user.${window.userId}`)
  .listen(".ai.streaming.started", (data) => {
   // Show streaming indicator
   showAiStreamingIndicator(data.conversation_id);
  })
  .listen(".ai.streaming.chunk", (data) => {
   // Append streaming content
   appendAiStreamingChunk(data.conversation_id, data.chunk);
  })
  .listen(".ai.streaming.completed", (data) => {
   // Hide streaming indicator, finalize response
   hideAiStreamingIndicator(data.conversation_id);
   finalizeAiResponse(data.conversation_id);
  })
  .listen(".ai.model.switched", (data) => {
   // Update model selection UI
   updateModelSelection(data.model);
  })
  .listen(".ai.conversation.saved", (data) => {
   // Update conversation history sidebar
   updateConversationHistory(data.conversation);
  })
  .listen(".ai.web.search.started", (data) => {
   // Show web search indicator
   showWebSearchIndicator(data.conversation_id);
  })
  .listen(".ai.web.search.completed", (data) => {
   // Hide web search indicator, show sources
   hideWebSearchIndicator(data.conversation_id);
   displayWebSources(data.sources);
  })
  .listen(".ai.faq.suggestion", (data) => {
   // Display FAQ suggestions
   displayFaqSuggestions(data.suggestions);
  })
  .listen(".ai.error.occurred", (data) => {
   // Show error message, suggest retry
   showAiError(data.error, data.conversation_id);
  })
  .listen(".ai.metrics.updated", (data) => {
   // Update admin dashboard metrics (admin only)
   if (window.userRole === 'admin' || window.userRole === 'superuser') {
     updateAiMetrics(data.metrics);
   }
  });
}

// Guests: AI conversation events (with conversation UUID)
if (window.conversationUuid && window.statusToken) {
 window.Echo.private(`conversation.${window.conversationUuid}`)
  .listen(".ai.streaming.started", (data) => {
   showAiStreamingIndicator(data.conversation_id);
  })
  .listen(".ai.streaming.chunk", (data) => {
   appendAiStreamingChunk(data.conversation_id, data.chunk);
  })
  .listen(".ai.streaming.completed", (data) => {
   hideAiStreamingIndicator(data.conversation_id);
   finalizeAiResponse(data.conversation_id);
  })
  .listen(".ai.model.switched", (data) => {
   updateModelSelection(data.model);
  })
  .listen(".ai.web.search.started", (data) => {
   showWebSearchIndicator(data.conversation_id);
  })
  .listen(".ai.web.search.completed", (data) => {
   hideWebSearchIndicator(data.conversation_id);
   displayWebSources(data.sources);
  })
  .listen(".ai.faq.suggestion", (data) => {
   displayFaqSuggestions(data.suggestions);
  })
  .listen(".ai.error.occurred", (data) => {
   showAiError(data.error, data.conversation_id);
  });
}

// AI Helper Functions
function showAiStreamingIndicator(conversationId) {
 const indicator = document.querySelector(`[data-conversation="${conversationId}"] .streaming-indicator`);
 if (indicator) {
  indicator.classList.remove('hidden');
  indicator.innerHTML = '<div class="flex items-center gap-2"><div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div><span class="text-sm text-gray-600">AI sedang menaip...</span></div>';
 }
}

function appendAiStreamingChunk(conversationId, chunk) {
 const messageContainer = document.querySelector(`[data-conversation="${conversationId}"] .streaming-message .streaming-content`);
 if (messageContainer) {
  messageContainer.innerHTML += chunk;
  // Auto-scroll to bottom
  messageContainer.scrollTop = messageContainer.scrollHeight;
 }
}

function hideAiStreamingIndicator(conversationId) {
 const indicator = document.querySelector(`[data-conversation="${conversationId}"] .streaming-indicator`);
 if (indicator) {
  indicator.classList.add('hidden');
 }
}

function finalizeAiResponse(conversationId) {
 const streamingMessage = document.querySelector(`[data-conversation="${conversationId}"] .streaming-message`);
 if (streamingMessage) {
  streamingMessage.classList.remove('streaming-message');
  streamingMessage.classList.add('ai-message', 'completed');
 }
}

function updateModelSelection(model) {
 const modelButtons = document.querySelectorAll('.model-selector button');
 modelButtons.forEach(button => {
  if (button.dataset.model === model) {
   button.classList.add('bg-primary-600', 'text-white');
   button.classList.remove('bg-white', 'text-gray-600');
  } else {
   button.classList.remove('bg-primary-600', 'text-white');
   button.classList.add('bg-white', 'text-gray-600');
  }
 });
}

function displayWebSources(sources) {
 const sourcesContainer = document.querySelector('.web-sources');
 if (sourcesContainer && sources.length > 0) {
  sourcesContainer.innerHTML = sources.map(source => 
   `<div class="flex items-center gap-2 text-sm">
     <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
     </svg>
     <a href="${source.url}" target="_blank" class="text-primary-600 hover:text-primary-700 truncate">${source.title}</a>
    </div>`
  ).join('');
  sourcesContainer.classList.remove('hidden');
 }
}

function displayFaqSuggestions(suggestions) {
 const suggestionsContainer = document.querySelector('.faq-suggestions');
 if (suggestionsContainer && suggestions.length > 0) {
  suggestionsContainer.innerHTML = suggestions.map(suggestion => 
   `<button onclick="selectFaqSuggestion('${suggestion}')" class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">${suggestion}</button>`
  ).join('');
  suggestionsContainer.classList.remove('hidden');
 }
}

function showAiError(error, conversationId) {
 const errorContainer = document.querySelector(`[data-conversation="${conversationId}"] .ai-error`);
 if (errorContainer) {
  errorContainer.innerHTML = `
   <div class="bg-red-50 border border-red-200 rounded-md p-3">
    <div class="flex">
     <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
     </svg>
     <div class="ml-3">
      <h3 class="text-sm font-medium text-red-800">Ralat AI</h3>
      <p class="text-sm text-red-700 mt-1">${error}</p>
      <button onclick="retryAiRequest('${conversationId}')" class="mt-2 text-sm bg-red-100 text-red-800 px-3 py-1 rounded-md hover:bg-red-200">Cuba Lagi</button>
     </div>
    </div>
   </div>
  `;
  errorContainer.classList.remove('hidden');
 }
}
   // Show Google SSO linked notification
  })
  .listen(".officer.assigned", (data) => {
   // Show Responsible Officer assigned notification
  });
}
```

---

## 4. PILIHAN PENYEDIA (Provider Options)

### 4.1. Laravel Reverb (Penyedia Utama - Disyorkan)

Laravel Reverb adalah pelayan WebSocket rasmi dari pasukan Laravel, direka
khusus untuk integrasi lancar dengan ekosistem Laravel.

**Kelebihan:**

- Dibangunkan oleh pasukan Laravel (sokongan rasmi)
- Sepenuhnya diurus sendiri (tiada penyedia pihak ketiga)
- Serasi dengan Pusher API (mudah untuk migrasi)
- Prestasi tinggi dengan sokongan horizontal scaling via Redis
- Tiada kos langganan bulanan

**Kerugian:**

- Memerlukan penyelenggaraan pelayan Reverb yang berjalan
- Kompleks untuk persediaan produksi multi-server

**Persediaan:**

```bash
# 1. Tetapkan di .env:
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=ictserve
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# 2. Tetapkan VITE variables di .env:
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# 3. Jalankan pelayan Reverb:
php artisan reverb:start

# 4. Jalankan pekerja baris gilir (terminal berasingan):
php artisan queue:work redis --queue=default

# 5. Bina frontend (ensure Node v22 is active):
# Option A (manual):
#+ npm ci && . .\.env.ps1 && npm run dev
# Option B (Windows helper):
# npm ci && npm run dev:win
```

### 4.2. Pusher (Penyedia Dihost - Alternatif)

**Kelebihan:**

- Dikelola sepenuhnya; tiada perlu menjalankan pelayan
- Tier percuma untuk pembangunan (Max 100 connections)
- Stabil dan teruji untuk produksi

**Kerugian:**

- Memerlukan akaun Pusher + kunci API (bayaran untuk produksi)
- Bukan penyelesaian yang diurus sendiri

**Persediaan:**

```bash
# 1. Daftar di https://pusher.com, dapatkan kunci API
# 2. Tetapkan di .env:
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=<your-app-id>
PUSHER_APP_KEY=<your-app-key>
PUSHER_APP_SECRET=<your-app-secret>
PUSHER_APP_CLUSTER=<your-cluster>

# 3. Tetapkan VITE variables di .env:
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST=api-<your-cluster>.pusher.com
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
```

---

## 5. INTEGRASI FRONTEND (Frontend Integration)

### 5.1. Inisialisasi Echo

Fail `resources/js/bootstrap.js` mengandungi logik inisialisasi Echo:

```javascript
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

// Reverb (penyedia utama)
const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;

if (reverbAppKey && reverbHost) {
 window.Echo = new Echo({
  broadcaster: "reverb",
  key: reverbAppKey,
  wsHost: reverbHost,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
  enabledTransports: ["ws", "wss"],
  disableStats: true,
 });
}

// Fallback ke Pusher jika Reverb tidak dikonfigurasi
const pusherAppKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherHost = import.meta.env.VITE_PUSHER_HOST;
if (!window.Echo && pusherAppKey && pusherHost) {
 window.Echo = new Echo({
  broadcaster: "pusher",
  key: pusherAppKey,
  wsHost: pusherHost,
  wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
  wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
  enabledTransports: ["ws", "wss"],
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? undefined,
  disableStats: true,
 });
}
```

### 5.2. Mendengarkan Saluran Peribadi (Hybrid)

Dalam komponen Livewire atau JavaScript:

```javascript
// Authenticated Users: Dengarkan saluran user (private-user.{id})
if (window.userId) {
 window.Echo.private(`user.${window.userId}`)
  .listen(".notification.created", (data) => {
   console.log("Notification received:", data);
   // Kemaskini UI
  })
  .listen(".status.updated", (data) => {
   console.log("Status updated:", data);
   // Kemaskini paparan status
  });
}

// Guests: Dengarkan saluran tiket (UUID-based dengan status token)
if (window.ticketUuid && window.statusToken) {
 window.Echo.private(`ticket.${window.ticketUuid}`)
  .listen(".notification.created", (data) => {
   console.log("Notification received:", data);
   // Kemaskini UI
  })
  .listen(".status.updated", (data) => {
   console.log("Status updated:", data);
   // Kemaskini paparan status
  });
}

// Guests: Dengarkan acara pinjaman aset (UUID-based)
if (window.loanUuid && window.statusToken) {
 window.Echo.private(`loan.${window.loanUuid}`).listen(
  ".asset.returned.damaged",
  (data) => {
   console.log("Asset damage reported:", data);
   // Kemaskini paparan aset
  }
 );
}
```

### 5.3. Integrasi Livewire

Livewire v3 menyokong integrasi Echo secara langsung menggunakan atribut `#[On]`:

```php
<?php

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    #[On('echo-private:user.{userId},notification.created')]
    public function handleNewNotification($event): void
    {
        $this->unreadCount++;
    }

    public function getListeners(): array
    {
        return [
            "echo-private:user.{$this->userId},.notification.created" => 'handleNewNotification',
        ];
    }
}
```

---

## 6. KEBENARAN SALURAN PERIBADI (Private Channel Authorization)

### 6.1. Takrifan Saluran (Hybrid Architecture)

Fail `routes/channels.php` mentakrifkan saluran peribadi dan kebenaran untuk authenticated users DAN guests:

```php
<?php

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;

// Saluran user untuk authenticated users (private-user.{id})
Broadcast::channel('user.{userId}', function ($user, int $userId) {
    return (int) $user->id === (int) $userId;
});

// Saluran tiket untuk Guests (UUID-based dengan status token)
Broadcast::channel('ticket.{uuid}', function ($user, string $uuid) {
    $ticket = HelpdeskTicket::where('uuid', $uuid)->first();

    if (!$ticket) {
        return false;
    }

    // Guest: Validate status token dari query parameter
    $statusToken = request()->query('status_token');
    if ($statusToken && Hash::check($ticket->uuid . $ticket->submitter_email, $statusToken)) {
        return ['uuid' => $ticket->uuid];
    }

    // Admin/Superuser: Validate policy
    if ($user && $user->can('view', $ticket)) {
        return ['uuid' => $ticket->uuid, 'role' => 'admin'];
    }

    return false;
});

// Saluran pinjaman untuk Guests (UUID-based dengan status token)
Broadcast::channel('loan.{uuid}', function ($user, string $uuid) {
    $loan = LoanApplication::where('uuid', $uuid)->first();

    if (!$loan) {
        return false;
    }

    // Guest: Validate status token dari query parameter
    $statusToken = request()->query('status_token');
    if ($statusToken && Hash::check($loan->uuid . $loan->applicant_email, $statusToken)) {
        return ['uuid' => $loan->uuid];
    }

    // Admin/Superuser: Validate policy
    if ($user && $user->can('view', $loan)) {
        return ['uuid' => $loan->uuid, 'role' => 'admin'];
    }

    return false;
});

// Saluran submission untuk ulasan (kekalkan untuk backward compatibility)
Broadcast::channel('submission.{type}.{id}', function ($user, $type, $id) {
    return match ($type) {
        'ticket' => $user && $user->can('view', HelpdeskTicket::find($id)),
        'loan' => $user && $user->can('view', LoanApplication::find($id)),
        default => false,
    };
});
```

### 6.2. Pengujian Kebenaran

Fail `tests/Feature/BroadcastingTest.php` mengandungi ujian kebenaran:

```php
public function test_authorizes_private_ticket_channel_for_guest_with_valid_token(): void
{
    $ticket = HelpdeskTicket::factory()->create([
        'uuid' => 'test-uuid-123',
        'submitter_email' => 'guest@example.com',
    ]);

    $statusToken = Hash::make($ticket->uuid . $ticket->submitter_email);

    config([
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.key' => 'test-key',
        'broadcasting.connections.pusher.secret' => 'test-secret',
        'broadcasting.connections.pusher.app_id' => 'test-app-id',
    ]);

    $response = $this->post('/broadcasting/auth', [
        'socket_id' => '123.456',
        'channel_name' => 'private-ticket.' . $ticket->uuid,
    ], [
        'X-Socket-Id' => '123.456',
    ])->withQueryParameters(['status_token' => $statusToken]);

    $response->assertStatus(200);
}

public function test_authorizes_private_loan_channel_for_admin(): void
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $loan = LoanApplication::factory()->create(['uuid' => 'loan-uuid-456']);

    config([
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.key' => 'test-key',
        'broadcasting.connections.pusher.secret' => 'test-secret',
        'broadcasting.connections.pusher.app_id' => 'test-app-id',
    ]);

    $response = $this->actingAs($admin)
        ->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-loan.' . $loan->uuid,
        ]);

    $response->assertStatus(200);
}
```

Untuk menjalankan ujian:

```bash
php artisan test tests/Feature/BroadcastingTest.php
```

---

## 7. PENGURUSAN PEKERJA BARIS GILIR (Queue Worker Management)

### 7.1. Memulakan Pekerja

Penyiaran menggunakan baris gilir untuk memproses pekerjaan secara asinkron:

```bash
# Dalam terminal berasingan, jalankan pekerja baris gilir
php artisan queue:work redis --queue=default

# Atau gunakan sync untuk pembangunan tempatan
QUEUE_CONNECTION=sync
```

### 7.2. Pengurusan Proses (Development)

Untuk pengembangan tempatan, gunakan `composer run dev` yang menjalankan semua
perkhidmatan secara serentak:

```bash
# Jalankan semua perkhidmatan pembangunan
composer run dev

# Atau jalankan secara berasingan:
php artisan serve          # Laravel server
php artisan reverb:start   # WebSocket server
php artisan queue:work     # Queue worker
npm run dev                # Vite dev server
```

### 7.3. Pengurusan Proses (Production)

Gunakan Supervisor untuk menguruskan proses Reverb dan queue worker:

```ini
[program:ictserve-reverb]
command=php /var/www/ictserve/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/reverb.log

[program:ictserve-queue]
command=php /var/www/ictserve/artisan queue:work redis --queue=default
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/queue.log
```

Lihat **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** § 6 untuk konfigurasi
Supervisor lengkap.

---

## 8. KESELAMATAN (Security Considerations)

### 8.1. Pengurusan Rahasia (Secret Management)

**Jangan sekali-kali** melakukan:

- Melakukan komit kunci API ke repositori
- Menyimpan `REVERB_APP_SECRET` dalam fail frontend
- Mendedahkan rahasia dalam konfigurasi umum

**Lakukan:**

- Tetapkan semua rahasia dalam `.env` (yang tidak dilakukan komit)
- Gunakan **GitHub Secrets** untuk CD/CI
- Putar kunci secara berkala di sistem produksi

### 8.2. Validasi Saluran (Channel Validation)

Sentiasa sahkan kebenaran di **backend** (`routes/channels.php`):

```php
// Betul ✓ - Guest dengan status token
Broadcast::channel('ticket.{uuid}', function ($user, string $uuid) {
    $ticket = HelpdeskTicket::where('uuid', $uuid)->first();
    $statusToken = request()->query('status_token');

    // Validasi status token untuk Guest
    if ($statusToken && Hash::check($ticket->uuid . $ticket->submitter_email, $statusToken)) {
        return ['uuid' => $ticket->uuid];
    }

    // Atau validasi policy untuk Admin
    return $user && $user->can('view', $ticket);
});

// Salah ✗
Broadcast::channel('ticket.{uuid}', function ($user, string $uuid) {
    return true;  // Tidak boleh! Membenarkan akses semua pengguna tanpa validasi
});
```

### 8.3. TLS/SSL untuk Produksi

Di produksi, sentiasa gunakan SSL/TLS:

```bash
# Untuk Laravel Reverb
REVERB_SCHEME=https
REVERB_PORT=443

# Konfigurasi TLS dalam config/reverb.php
'options' => [
    'tls' => [
        'local_cert' => '/path/to/cert.pem',
        'local_pk' => '/path/to/key.pem',
    ],
],
```

### 8.4. Horizontal Scaling

Untuk persediaan multi-server, aktifkan scaling via Redis:

```bash
REVERB_SCALING_ENABLED=true
REDIS_HOST=your-redis-server
```

---

## 9. PEMECAHAN MASALAH (Troubleshooting)

### Masalah: "WebSocket connection failed"

**Sebab Kemungkinan:**

- Pelayan Reverb tidak berjalan
- Port salah (biasanya 8080 untuk Reverb)
- Firewall menyekat koneksi WebSocket

**Penyelesaian:**

```bash
# Periksa pelayan berjalan
netstat -an | grep 8080

# Mulakan semula Reverb
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Masalah: "Channel private-ticket.X unauthorized"

**Sebab Kemungkinan:**

- Status token tidak sah atau tidak disediakan (Guest)
- Logik kebenaran di `routes/channels.php` salah
- UUID tiket tidak wujud dalam database

**Penyelesaian:**

```php
// Periksa logik dalam routes/channels.php
Broadcast::channel('ticket.{uuid}', function ($user, string $uuid) {
    $ticket = HelpdeskTicket::where('uuid', $uuid)->first();
    $statusToken = request()->query('status_token');

    // Debug: dd($ticket, $statusToken, $user);

    if ($statusToken && Hash::check($ticket->uuid . $ticket->submitter_email, $statusToken)) {
        return ['uuid' => $ticket->uuid];
    }

    return $user && $user->can('view', $ticket);
});
```

### Masalah: "Queue jobs not processing"

**Sebab Kemungkinan:**

- Pekerja baris gilir tidak berjalan
- Redis tidak dapat dihubungi
- `QUEUE_CONNECTION` salah

**Penyelesaian:**

```bash
# Periksa pekerja berjalan
ps aux | grep "queue:work"

# Atau mulakan semula
php artisan queue:work redis --queue=default

# Periksa sambungan Redis
php artisan tinker
>>> Redis::ping()  // Jika berjaya, menghasilkan PONG
```

### Masalah: "Echo not initialized"

**Sebab Kemungkinan:**

- Pembolehubah persekitaran VITE tidak ditetapkan
- Frontend tidak dibina semula selepas perubahan `.env`

**Penyelesaian:**

```bash
# Pastikan pembolehubah VITE ditetapkan dalam .env
VITE_REVERB_APP_KEY=your-key
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http

# Bina semula frontend
npm run build
```

---

## 10. INTEGRASI LARAVEL PULSE (Laravel Pulse Integration) - v3.5.0

### 10.1. Pemantauan Prestasi Penyiaran (Broadcasting Performance Monitoring)

Laravel Pulse v1.3.0 menyediakan pemantauan prestasi masa nyata untuk operasi penyiaran:

**Metrik yang Dipantau:**

| Metrik                    | Threshold | Tindakan                                      |
| ------------------------- | --------- | --------------------------------------------- |
| WebSocket Connection Time | <100ms    | Alert jika melebihi threshold                 |
| Message Delivery Latency  | <500ms    | Log slow deliveries                           |
| Queue Job Processing      | <2s       | Monitor broadcast job execution               |
| Failed Broadcasts         | <1%       | Alert admin untuk kadar kegagalan tinggi      |

**Konfigurasi Pulse untuk Broadcasting:**

```php
// config/pulse.php
'recorders' => [
    \Laravel\Pulse\Recorders\SlowJobs::class => [
        'enabled' => true,
        'threshold' => 1000, // 1 second
        'sample_rate' => 1,
    ],
    \Laravel\Pulse\Recorders\Queues::class => [
        'enabled' => true,
        'sample_rate' => 1,
    ],
],
```

### 10.2. Dashboard Pulse untuk Broadcasting

Akses dashboard Pulse di `/pulse` (admin & superuser sahaja):

- **Queue Metrics**: Monitor broadcast job queue depth dan processing time
- **Slow Jobs**: Identify slow broadcast events
- **Server Health**: CPU/memory usage semasa peak broadcasting

---

## 11. INTEGRASI API SANCTUM (Sanctum API Integration) - v3.5.0

### 11.1. Broadcasting untuk API Clients

API clients yang menggunakan Laravel Sanctum tokens boleh subscribe ke WebSocket channels:

```javascript
// API Client dengan Sanctum Token
const token = "your-sanctum-token";

window.Echo = new Echo({
 broadcaster: "reverb",
 key: reverbAppKey,
 wsHost: reverbHost,
 wsPort: reverbPort,
 forceTLS: true,
 auth: {
  headers: {
   Authorization: `Bearer ${token}`,
  },
 },
});

// Subscribe ke private channel
window.Echo.private(`user.${userId}`).listen(".notification.created", (data) => {
 console.log("API notification:", data);
});
```

### 11.2. Channel Authorization untuk API Tokens

```php
// routes/channels.php - API Token Support
Broadcast::channel('user.{userId}', function ($user, int $userId) {
    // Support both session auth and Sanctum token auth
    if ($user && (int) $user->id === (int) $userId) {
        return ['id' => $user->id, 'name' => $user->name];
    }
    return false;
});
```

---

## 12. RUJUKAN LANJUTAN (Advanced References)

| Rujukan              | Pautan                                                                 | Catatan                    |
| -------------------- | ---------------------------------------------------------------------- | -------------------------- |
| Laravel Broadcasting | [laravel.com/docs/broadcasting](https://laravel.com/docs/broadcasting) | Dokumentasi rasmi          |
| Laravel Reverb       | [laravel.com/docs/reverb](https://laravel.com/docs/reverb)             | Panduan Reverb             |
| Laravel Pulse        | [laravel.com/docs/pulse](https://laravel.com/docs/pulse)               | Performance monitoring     |
| Laravel Sanctum      | [laravel.com/docs/sanctum](https://laravel.com/docs/sanctum)           | API token authentication   |
| Laravel Socialite    | [laravel.com/docs/socialite](https://laravel.com/docs/socialite)       | Google OAuth SSO           |
| Pusher Channels      | [pusher.com/channels](https://pusher.com/channels)                     | Panduan Pusher             |
| RFC 6455             | [tools.ietf.org](https://tools.ietf.org/html/rfc6455)                  | Spesifikasi WebSocket      |
| OWASP WebSocket      | [owasp.org](https://owasp.org/www-community/attacks/)                  | Keselamatan WebSocket      |

---

## Pengesahan Dokumen (Document Certification)

| Peranan   | Nama                    | Tandatangan | Tarikh          |
| --------- | ----------------------- | ----------- | --------------- |
| Penulis   | Pasukan Pembangunan BPM | -           | 1 Disember 2025 |
| Penyemak  | -                       | -           | -               |
| Kelulusan | -                       | -           | -               |

---

**© 2025 BPM MOTAC. Hakcipta Terpelihara. Terhad kepada kegunaan dalaman sahaja.**
