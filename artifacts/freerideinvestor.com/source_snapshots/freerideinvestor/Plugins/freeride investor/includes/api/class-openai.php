<?php

if (!defined('ABSPATH')) exit;

class FRI_OpenAI {

    private static $base_url = 'https://api.openai.com/v1/chat/completions';
    private static $api_key = OPENAI_API_KEY;

    /**
     * Analyze sentiment for news headlines
     *
     * @param array $headlines An array of news headlines.
     * @return float|WP_Error The average sentiment score or WP_Error on failure.
     */
    public static function analyze_sentiment($headlines) {
        $headlines = array_slice($headlines, 0, 10); // Limit to 10 headlines

        $prompt = "Analyze the sentiment of the following headlines. Provide a JSON object with 'average_sentiment':\n\n";
        foreach ($headlines as $headline) {
            $prompt .= "- $headline\n";
        }

        $response = self::send_request([
            'model'       => 'gpt-3.5-turbo',
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a sentiment analysis bot.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens'  => 60,
            'temperature' => 0,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        // Parse sentiment score
        $content = $response['choices'][0]['message']['content'];
        preg_match('/"average_sentiment":\s*(-?\d+(\.\d+)?)/', $content, $matches);

        return isset($matches[1]) ? floatval($matches[1]) : 0; // Default to neutral
    }

    /**
     * Generate AI-based trade plan
     *
     * @param string $symbol The stock symbol.
     * @param array $stock_data The stock data.
     * @param float $sentiment The sentiment score.
     * @return string|WP_Error The trade plan or WP_Error on failure.
     */
    public static function generate_trade_plan($symbol, $stock_data, $sentiment) {
        $prompt = "Create a day trading plan for:\n- Symbol: $symbol\n- Price: {$stock_data['price']}\n- Sentiment: $sentiment";

        $response = self::send_request([
            'model'       => 'gpt-3.5-turbo',
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a professional stock trading assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens'  => 300,
            'temperature' => 0.7,
        ]);

        return is_wp_error($response) ? $response : $response['choices'][0]['message']['content'];
    }

    /**
     * Send a request to OpenAI API
     *
     * @param array $body Request body.
     * @return array|WP_Error The API response or WP_Error on failure.
     */
    private static function send_request($body) {
        $headers = FRI_API_Requests::get_default_headers(self::$api_key);

        return FRI_API_Requests::make_request(self::$base_url, 'POST', json_encode($body), $headers);
    }
}
