<?php

namespace App\Support;

class ProductDescriptionGenerator
{
    private const SEPARATOR = '|||AR|||';

    private const CATEGORY_AR = [
        'Mobile & Tablets' => 'الهواتف والأجهزة اللوحية',
        'Laptops & Computers' => 'الحواسيب المحمولة والمكتبية',
        'Cameras & Photography' => 'الكاميرات والتصوير',
        'Audio & Headphones' => 'الصوت وسماعات الرأس',
        'TVs & Monitors' => 'التلفزيونات والشاشات',
        'Gaming' => 'الألعاب',
        'Smart Home' => 'المنزل الذكي',
        'Accessories' => 'الإكسسوارات',
    ];

    public static function english(string $name, string $category): string
    {
        $categoryLabel = strtolower($category);

        return implode("\n", [
            "{$name} — premium {$categoryLabel} from Electro.",
            'Reliable performance for everyday use and demanding tasks.',
            'Official warranty. Fast delivery across Syria.',
            'Features: high build quality, energy-efficient design, and full compatibility with modern accessories.',
        ]);
    }

    public static function arabic(string $name, string $category): string
    {
        $categoryAr = self::CATEGORY_AR[$category] ?? $category;

        return implode("\n", [
            "{$name} — منتج مميز من فئة {$categoryAr} من إلكترو.",
            'أداء موثوق للاستخدام اليومي والمهام المتقدمة.',
            'ضمان رسمي. توصيل سريع في جميع أنحاء سوريا.',
            'المميزات: جودة تصنيع عالية، تصميم موفر للطاقة، وتوافق كامل مع الإكسسوارات الحديثة.',
        ]);
    }

    /**
     * @return array{en: string, ar: string}
     */
    public static function pair(string $name, string $category): array
    {
        return [
            'en' => self::english($name, $category),
            'ar' => self::arabic($name, $category),
        ];
    }

    /**
     * @return array{en: string, ar: string}
     */
    public static function fromLegacy(?string $description, string $name, string $category): array
    {
        $text = trim((string) $description);
        $pos = strpos($text, self::SEPARATOR);

        if ($pos !== false) {
            return [
                'en' => trim(substr($text, 0, $pos)),
                'ar' => trim(substr($text, $pos + strlen(self::SEPARATOR))),
            ];
        }

        return self::pair($name, $category);
    }
}
