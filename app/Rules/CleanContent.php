<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CleanContent implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || !is_string($value)) {
            return;
        }

        // Lấy danh sách từ cấm từ file cấu hình
        $blacklist = config('profanity.blacklist', []);
        
        // Chuẩn hóa chuỗi đầu vào (chữ thường, UTF-8)
        $normalizedValue = mb_strtolower($value, 'UTF-8');

        // Tách chuỗi đầu vào thành các từ đơn để kiểm tra chính xác (tránh false positive như "cl" trong "click")
        $tokens = preg_split('/[\s,\.\?\!\:\;\-\_\+\=\(\)\[\]\{\}\<\>\"\'\/]+/u', $normalizedValue);
        $tokens = array_filter($tokens);

        foreach ($blacklist as $word) {
            $normalizedWord = mb_strtolower($word, 'UTF-8');
            
            if (str_contains($normalizedWord, ' ')) {
                // Nếu từ cấm là cụm từ (có khoảng trắng), ví dụ "óc chó", "khốn nạn"
                if (str_contains($normalizedValue, $normalizedWord)) {
                    $fail('Nội dung chứa từ ngữ không phù hợp, vui lòng chỉnh sửa.');
                    return;
                }
            } else {
                // Nếu từ cấm là từ đơn hoặc từ viết tắt (ví dụ "vcl", "cl", "vl", "cc")
                if (in_array($normalizedWord, $tokens)) {
                    $fail('Nội dung chứa từ ngữ không phù hợp, vui lòng chỉnh sửa.');
                    return;
                }
            }
        }
    }
}
