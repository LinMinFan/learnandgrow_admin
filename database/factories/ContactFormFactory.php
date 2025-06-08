<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactForm>
 */
class ContactFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->getRandomSubject(),
            'name' => $this->getRandomChineseName(),
            'email' => $this->faker->safeEmail,
            'phone' => $this->getRandomTaiwanPhone(),
            'message' => $this->getRandomChineseMessage(),
            'is_read' => $this->faker->boolean(10), // 10% 機率為已讀
            'read_at' => null, // 稍後可根據 is_read 動態處理
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($contact) {
            if ($contact->is_read && !$contact->read_at) {
                $contact->read_at = now();
            }
        });
    }

    /**
     * 隨機生成中文姓名
     */
    private function getRandomChineseName(): string
    {
        $surnames = ['王', '李', '張', '劉', '陳', '楊', '趙', '黃', '周', '吳', '徐', '孫', '胡', '朱', '高', '林', '何', '郭', '馬', '羅', '梁', '宋', '鄭', '謝', '韓', '唐', '馮', '于', '董', '蕭', '程', '曹', '袁', '鄧', '許', '傅', '沈', '曾', '彭', '呂'];

        $givenNames = ['志明', '美玲', '建華', '淑芬', '俊傑', '麗華', '文雄', '淑惠', '宗翰', '雅婷', '承翰', '怡君', '志豪', '慧玲', '宏偉', '佳穎', '銘洋', '雅雯', '俊宏', '淑敏', '國華', '麗君', '文華', '美慧', '志強', '雅芳', '建宏', '淑娟', '俊男', '美如'];

        return $this->faker->randomElement($surnames) . $this->faker->randomElement($givenNames);
    }

    /**
     * 隨機生成台灣手機號碼
     */
    private function getRandomTaiwanPhone(): string
    {
        $prefixes = ['09'];
        $secondDigits = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return $this->faker->randomElement($prefixes) .
            $this->faker->randomElement($secondDigits) .
            $this->faker->numerify('########');
    }

    /**
     * 隨機生成中文主題
     */
    private function getRandomSubject(): string
    {
        $subjects = [
            '產品諮詢',
            '服務建議',
            '技術問題',
            '合作提案',
            '價格詢問',
            '訂單查詢',
            '售後服務',
            '投訴建議',
            '功能請求',
            '帳號問題',
            '付款問題',
            '配送查詢',
            '退換貨申請',
            '會員服務',
            '系統異常回報',
            '商業合作',
            '媒體採訪',
            '招商加盟',
            '人力資源',
            '其他問題'
        ];

        return $this->faker->randomElement($subjects);
    }

    /**
     * 隨機生成中文訊息內容
     */
    private function getRandomChineseMessage(): string
    {
        $messages = [
            '您好，我對貴公司的產品很有興趣，希望能夠進一步了解相關資訊，包括價格、規格以及交期等細節，期待您的回覆。',
            '想請問一下最新的服務方案有什麼特色？我們公司正在評估不同的解決方案，希望能夠安排時間進行詳細討論。',
            '在使用過程中遇到一些技術問題，系統偶爾會出現異常，希望能夠得到技術支援，麻煩安排相關人員協助處理。',
            '我們是一家中型企業，對於貴公司提供的服務很感興趣，希望能夠討論合作的可能性，包括客製化需求的部分。',
            '請問目前的優惠方案還有效嗎？我們預計採購數量較大，是否有大量採購的特殊價格？期待您的回覆。',
            '上週訂購的商品到現在還沒有收到，想確認一下目前的配送狀況，以及預計的到貨時間，謝謝。',
            '產品使用上有些疑問，說明書中的部分內容不太清楚，希望能夠提供更詳細的操作指南或安排教學。',
            '對於服務品質有一些建議，希望貴公司能夠參考並改善，整體來說還是很滿意的，期待持續的合作關係。',
            '想建議新增一些功能，這些功能對我們的業務會很有幫助，不知道是否有計劃在未來的版本中加入？',
            '帳號登入時出現問題，嘗試重設密碼但沒有收到郵件，麻煩協助處理，或提供其他的解決方案。',
            '付款後發現金額有誤差，想確認一下收費標準，以及如何申請退款或調整，麻煩盡快處理。',
            '想了解貴公司是否有經銷商制度，我們有意願成為區域代理，希望能夠進一步討論合作條件。',
            '媒體採訪邀請，我們是知名科技雜誌，希望能夠安排專訪，介紹貴公司的創新產品和發展理念。',
            '人才招募詢問，看到貴公司在徵才，想了解工作內容和薪資福利，以及應徵的相關流程，謝謝。',
            '系統維護通知看到了，想確認維護期間是否會影響正常使用，以及有沒有替代方案可以使用。'
        ];

        return $this->faker->randomElement($messages);
    }
}
