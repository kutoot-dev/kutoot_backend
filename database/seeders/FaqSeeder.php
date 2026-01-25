<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * FaqSeeder - Seeds frequently asked questions.
 *
 * DEV ONLY: Creates sample FAQs for the help/support section.
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding FAQs...');

        $faqs = [
            [
                'question' => 'How do I place an order?',
                'answer' => 'To place an order, browse our products, add items to your cart, and proceed to checkout. Follow the steps to enter your shipping address and payment information to complete your purchase.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept a variety of payment methods including credit/debit cards (Visa, MasterCard, American Express), PayPal, and Cash on Delivery (COD) for eligible locations.',
            ],
            [
                'question' => 'How can I track my order?',
                'answer' => 'Once your order is shipped, you will receive a tracking number via email. You can use this number to track your package on our website or the courier partner website.',
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for most products. Items must be unused, in original packaging, with tags attached. Some categories like electronics have specific return windows. Contact support for return authorization.',
            ],
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Standard shipping typically takes 5-7 business days. Express shipping is available for 2-3 business day delivery. International orders may take 10-15 business days depending on the destination.',
            ],
            [
                'question' => 'Do you offer international shipping?',
                'answer' => 'Yes, we ship to over 50 countries worldwide. Shipping costs and delivery times vary by destination. You can check shipping availability by entering your address during checkout.',
            ],
            [
                'question' => 'How do I contact customer support?',
                'answer' => 'You can reach our customer support team via email at support@kutoot.com, through our live chat feature on the website, or by calling our helpline during business hours (9 AM - 6 PM IST).',
            ],
            [
                'question' => 'Can I modify or cancel my order?',
                'answer' => 'Orders can be modified or canceled within 2 hours of placing them. After this window, once the order enters processing, modifications are not possible. Please contact support immediately if you need to make changes.',
            ],
            [
                'question' => 'How do I apply a coupon code?',
                'answer' => 'During checkout, you will see a field labeled "Promo Code" or "Coupon Code". Enter your code and click "Apply" to see the discount reflected in your order total.',
            ],
            [
                'question' => 'Are my personal and payment details secure?',
                'answer' => 'Yes, we use industry-standard SSL encryption to protect all your personal and payment information. We never store your complete credit card details on our servers.',
            ],
        ];

        foreach ($faqs as $index => $faqData) {
            Faq::updateOrCreate(
                ['question' => $faqData['question']],
                [
                    'question' => $faqData['question'],
                    'answer' => $faqData['answer'],
                    'status' => 1,
                    'serial' => $index + 1,
                ]
            );

            $this->command->line("  ✓ FAQ: " . Str::limit($faqData['question'], 50));
        }

        $this->command->info('FaqSeeder completed. ' . count($faqs) . ' FAQs seeded.');
    }
}
