<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use WebSocket\Client as WebSocketClient;

class RealtimeRelayService implements MessageComponentInterface
{
    protected $clients;

    protected $openaiConnections = [];

    private string $openaiRealtimeUrl = 'wss://api.openai.com/v1/realtime';

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * Get the API key - prefer user's key over env key
     */
    private function getApiKey(?User $user = null): ?string
    {
        // Check if there's a user with an API key
        if ($user && $user->openai_api_key) {
            return $user->openai_api_key;
        }

        // Fall back to environment variable
        return config('openai.api_key');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);

        // Create a connection to OpenAI for this client
        $this->connectToOpenAI($conn);

        Log::info("New WebSocket connection: {$conn->resourceId}");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        // Relay message to OpenAI
        if (isset($this->openaiConnections[$from->resourceId])) {
            $openaiConn = $this->openaiConnections[$from->resourceId];

            // Add authentication if this is a session update
            if ($data['type'] === 'session.update') {
                $data['session']['model'] = 'gpt-4o-realtime-preview-2024-12-17';
            }

            $openaiConn->send(json_encode($data));

            Log::info("Relayed message to OpenAI from client {$from->resourceId}");
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Close OpenAI connection
        if (isset($this->openaiConnections[$conn->resourceId])) {
            $this->openaiConnections[$conn->resourceId]->close();
            unset($this->openaiConnections[$conn->resourceId]);
        }

        // Remove client
        $this->clients->detach($conn);

        Log::info("Connection {$conn->resourceId} disconnected");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::error("WebSocket error: {$e->getMessage()}");
        $conn->close();
    }

    private function connectToOpenAI(ConnectionInterface $clientConn)
    {
        try {
            // Get API key (for now, use the env key - in a real app, you'd pass user info)
            $apiKey = $this->getApiKey();

            if (empty($apiKey)) {
                throw new \Exception('No OpenAI API key available');
            }

            // Create WebSocket connection to OpenAI
            $headers = [
                'Authorization' => 'Bearer '.$apiKey,
                'OpenAI-Beta' => 'realtime=v1',
            ];

            $openaiConn = new WebSocketClient($this->openaiRealtimeUrl, [
                'timeout' => 60,
                'headers' => $headers,
            ]);

            // Store the connection
            $this->openaiConnections[$clientConn->resourceId] = $openaiConn;

            // Set up message handler for OpenAI responses
            $this->setupOpenAIHandlers($clientConn, $openaiConn);

            Log::info("Connected to OpenAI Realtime API for client {$clientConn->resourceId}");

        } catch (\Exception $e) {
            Log::error("Failed to connect to OpenAI: {$e->getMessage()}");
            $clientConn->send(json_encode([
                'type' => 'error',
                'error' => [
                    'message' => 'Failed to connect to OpenAI Realtime API',
                    'details' => $e->getMessage(),
                ],
            ]));
        }
    }

    private function setupOpenAIHandlers(ConnectionInterface $clientConn, WebSocketClient $openaiConn)
    {
        // Start a loop to receive messages from OpenAI
        // This would typically run in a separate thread/process
        // For now, we'll handle it synchronously

        try {
            while ($clientConn->resourceId && isset($this->openaiConnections[$clientConn->resourceId])) {
                $message = $openaiConn->receive();

                if ($message) {
                    // Relay OpenAI response back to client
                    $clientConn->send($message);

                    Log::info("Relayed OpenAI response to client {$clientConn->resourceId}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error in OpenAI message loop: {$e->getMessage()}");
        }
    }
}
