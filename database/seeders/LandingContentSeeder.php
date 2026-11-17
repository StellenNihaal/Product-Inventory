<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandingContentSeeder extends Seeder
{
    public function run()
    {
        $contents = [
            // Announcement
            ['key' => 'announce_text',      'section' => 'announce', 'label' => 'Announcement Bar Text',   'value' => 'Free shipping on orders above ₹999'],

            // Hero
            ['key' => 'hero_eyebrow',       'section' => 'hero',     'label' => 'Hero Eyebrow Text',       'value' => 'Considered Products'],
            ['key' => 'hero_title',         'section' => 'hero',     'label' => 'Hero Title',              'value' => 'Manage your inventory with ease.'],
            ['key' => 'hero_italic_word',   'section' => 'hero',     'label' => 'Hero Italic Word',        'value' => 'ease'],
            ['key' => 'hero_subtitle',      'section' => 'hero',     'label' => 'Hero Subtitle',           'value' => 'A powerful inventory platform with real-time search, smart cart management, and a complete admin panel — all in one place.'],
            ['key' => 'hero_btn_primary',   'section' => 'hero',     'label' => 'Hero Primary Button',     'value' => 'Browse the collection'],
            ['key' => 'hero_btn_secondary', 'section' => 'hero',     'label' => 'Hero Secondary Button',   'value' => 'Login to dashboard'],
            ['key' => 'hero_chip1_label',   'section' => 'hero',     'label' => 'Hero Chip 1 Label',       'value' => 'Stock'],
            ['key' => 'hero_chip2_label',   'section' => 'hero',     'label' => 'Hero Chip 2 Label',       'value' => 'Users'],

            // Categories section
            ['key' => 'cat_section_label',  'section' => 'categories', 'label' => 'Categories Section Label', 'value' => 'Shop by collection'],
            ['key' => 'cat_title',          'section' => 'categories', 'label' => 'Categories Title',        'value' => 'Everything, sorted the way you actually shop.'],
            ['key' => 'cat_italic_word',    'section' => 'categories', 'label' => 'Categories Italic Word',  'value' => 'shop'],
            ['key' => 'cat_subtitle',       'section' => 'categories', 'label' => 'Categories Subtitle',     'value' => 'Browse our focused collections — only products that made the cut.'],

            // Explore band
            ['key' => 'explore_title',      'section' => 'explore',  'label' => 'Explore Band Title',      'value' => 'Not sure where to start?'],
            ['key' => 'explore_italic',     'section' => 'explore',  'label' => 'Explore Italic Text',     'value' => 'Browse it all.'],
            ['key' => 'explore_subtitle',   'section' => 'explore',  'label' => 'Explore Subtitle',        'value' => 'products across every collection, in one place.'],
            ['key' => 'explore_btn',        'section' => 'explore',  'label' => 'Explore Button Text',     'value' => 'View all products'],

            // Footer
            ['key' => 'footer_brand_desc',  'section' => 'footer',   'label' => 'Footer Brand Description', 'value' => 'A smart, modern inventory system built for speed and simplicity.'],
            ['key' => 'footer_instagram',   'section' => 'footer',   'label' => 'Footer Instagram URL',    'value' => '#'],
            ['key' => 'footer_twitter',     'section' => 'footer',   'label' => 'Footer Twitter URL',      'value' => '#'],
            ['key' => 'footer_linkedin',    'section' => 'footer',   'label' => 'Footer LinkedIn URL',     'value' => '#'],
            ['key' => 'footer_copyright',   'section' => 'footer',   'label' => 'Footer Copyright Text',   'value' => 'Product Inventory. All rights reserved.'],
        ];

        foreach ($contents as $content) {
            DB::table('landing_contents')->updateOrInsert(
                ['key' => $content['key']],
                array_merge($content, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
