<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class AdminSettingController extends Controller
{
    public function index()
    {
        $customPath = public_path('custom_notification.mp3');
        $hasCustom = File::exists($customPath);

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.settings', [
            'hasCustom' => $hasCustom,
            'soundUrl'  => $hasCustom ? asset('custom_notification.mp3') : asset('assets/audio/default.mp3'),
            'categories' => $categories,
            'cafeIsOpen' => Setting::isCafeOpen(),

            // ── Receipt settings ────────────────────────────────────────
            // Logo
            'receiptLogo'           => Setting::getValue('receipt_logo'),
            'receiptShowLogo'       => Setting::getValue('receipt_show_logo', '0'),
            'receiptLogoSize'       => Setting::getValue('receipt_logo_size', '64'),
            'receiptLogoSpacing'    => Setting::getValue('receipt_logo_spacing', '10'),

            // Identitas
            'receiptCafeLocation'   => Setting::getValue('receipt_cafe_location'),
            'receiptWhatsapp'       => Setting::getValue('receipt_whatsapp'),
            'receiptShowTagline'    => Setting::getValue('receipt_show_tagline', '1'),

            // Tipografi
            'receiptFontFamily'     => Setting::getValue('receipt_font_family', 'system'),
            'receiptHeaderFontSize' => Setting::getValue('receipt_header_font_size', '20'),
            'receiptBodyFontSize'   => Setting::getValue('receipt_body_font_size', '13'),

            // Warna & Tema
            'receiptTheme'          => Setting::getValue('receipt_theme', 'normal'),
            'receiptAccentColor'    => Setting::getValue('receipt_accent_color', '#1a1a1a'),
            'receiptTotalColor'     => Setting::getValue('receipt_total_color', '#1a1a1a'),

            // Garis & Batas
            'receiptBorderStyle'    => Setting::getValue('receipt_border_style', 'dashed'),
            'receiptBorderColor'    => Setting::getValue('receipt_border_color', '#e5e5e5'),

            // Ukuran Kertas
            'receiptPaperSize'      => Setting::getValue('receipt_paper_size', 'thermal80'),

            // Elemen Ditampilkan
            'receiptShowCustomer'   => Setting::getValue('receipt_show_customer_info', '1'),
            'receiptShowPayment'    => Setting::getValue('receipt_show_payment_method', '1'),
            'receiptShowItemNotes'  => Setting::getValue('receipt_show_item_notes', '1'),
            'receiptShowOrderType'  => Setting::getValue('receipt_show_order_type', '1'),
            'receiptShowTableNo'    => Setting::getValue('receipt_show_table_no', '1'),

            // Footer
            'receiptFooterText'     => Setting::getValue('receipt_footer_text', 'Terima kasih! 🙏'),
            'receiptFooterSecondary'=> Setting::getValue('receipt_footer_secondary'),
            'receiptFooterFontSize' => Setting::getValue('receipt_footer_font_size', '14'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'notification_sound' => 'required|file|mimes:mp3,wav|max:2048',
        ]);

        if ($request->hasFile('notification_sound')) {
            $file = $request->file('notification_sound');
            $file->move(public_path(), 'custom_notification.mp3');
            
            return back()->with('success', 'Suara notifikasi berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    public function updateReceipt(Request $request)
    {
        $request->validate([
            'receipt_logo'              => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            'receipt_cafe_location'     => 'nullable|string|max:500',
            'receipt_whatsapp'          => 'nullable|string|max:20',
            'receipt_footer_text'       => 'nullable|string|max:200',
            'receipt_footer_secondary'  => 'nullable|string|max:200',
            'receipt_theme'             => 'required|in:normal,bw,dark,blue',
            'receipt_border_style'      => 'nullable|in:dashed,solid,none',
            'receipt_paper_size'        => 'nullable|in:thermal80,a5,a4',
            'receipt_font_family'       => 'nullable|in:system,serif,mono',
            'receipt_accent_color'      => 'nullable|string|max:7',
            'receipt_total_color'       => 'nullable|string|max:7',
            'receipt_border_color'      => 'nullable|string|max:7',
        ]);

        // Handle logo upload
        if ($request->hasFile('receipt_logo')) {
            $oldLogo = Setting::getValue('receipt_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }
            $file = $request->file('receipt_logo');
            $filename = 'receipt_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            Setting::setValue('receipt_logo', 'uploads/' . $filename);
        }

        // Handle logo removal
        if ($request->boolean('remove_logo')) {
            $oldLogo = Setting::getValue('receipt_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }
            Setting::setValue('receipt_logo', null);
            Setting::setValue('receipt_show_logo', '0');
        }

        // Accent color: prefer color picker value (hex input text synced via JS)
        $accentColor = $request->input('receipt_accent_color_text') ?: $request->input('receipt_accent_color', '#1a1a1a');
        $totalColor  = $request->input('receipt_total_color_text')  ?: $request->input('receipt_total_color', '#1a1a1a');
        $borderColor = $request->input('receipt_border_color_text') ?: $request->input('receipt_border_color', '#e5e5e5');

        // Save ALL settings
        Setting::setValue('receipt_show_logo',           $request->boolean('receipt_show_logo') ? '1' : '0');
        Setting::setValue('receipt_logo_size',            $request->input('receipt_logo_size', '64'));
        Setting::setValue('receipt_logo_spacing',         $request->input('receipt_logo_spacing', '10'));
        Setting::setValue('receipt_cafe_location',        $request->input('receipt_cafe_location'));
        Setting::setValue('receipt_whatsapp',             $request->input('receipt_whatsapp'));
        Setting::setValue('receipt_show_tagline',         $request->boolean('receipt_show_tagline') ? '1' : '0');
        Setting::setValue('receipt_font_family',          $request->input('receipt_font_family', 'system'));
        Setting::setValue('receipt_header_font_size',     $request->input('receipt_header_font_size', '20'));
        Setting::setValue('receipt_body_font_size',       $request->input('receipt_body_font_size', '13'));
        Setting::setValue('receipt_theme',                $request->input('receipt_theme', 'normal'));
        Setting::setValue('receipt_accent_color',         $accentColor);
        Setting::setValue('receipt_total_color',          $totalColor);
        Setting::setValue('receipt_border_style',         $request->input('receipt_border_style', 'dashed'));
        Setting::setValue('receipt_border_color',         $borderColor);
        Setting::setValue('receipt_paper_size',           $request->input('receipt_paper_size', 'thermal80'));
        Setting::setValue('receipt_show_customer_info',   $request->boolean('receipt_show_customer_info') ? '1' : '0');
        Setting::setValue('receipt_show_payment_method',  $request->boolean('receipt_show_payment_method') ? '1' : '0');
        Setting::setValue('receipt_show_item_notes',      $request->boolean('receipt_show_item_notes') ? '1' : '0');
        Setting::setValue('receipt_show_tagline_header',  $request->boolean('receipt_show_tagline_header') ? '1' : '0');
        Setting::setValue('receipt_show_order_type',      $request->boolean('receipt_show_order_type') ? '1' : '0');
        Setting::setValue('receipt_show_table_no',        $request->boolean('receipt_show_table_no') ? '1' : '0');
        Setting::setValue('receipt_footer_text',          $request->input('receipt_footer_text', 'Terima kasih! 🙏'));
        Setting::setValue('receipt_footer_secondary',     $request->input('receipt_footer_secondary'));
        Setting::setValue('receipt_footer_font_size',     $request->input('receipt_footer_font_size', '14'));

        return back()->with('success', 'Pengaturan struk berhasil disimpan!');
    }

    public function updateCloseOrder(Request $request)
    {
        $request->validate([
            'close_order_time' => 'required|array',
            'close_order_time.*' => 'nullable|date_format:H:i',
        ]);

        $times = $request->input('close_order_time', []);

        foreach ($times as $categoryId => $time) {
            Category::where('id', $categoryId)->update([
                'close_order_time' => $time ?: null,
            ]);
        }

        // Clear cached categories so changes take effect immediately
        Cache::forget('menu_categories');

        return back()->with('success', 'Pengaturan close order berhasil disimpan!');
    }

    public function toggleCafe(Request $request)
    {
        $currentStatus = Setting::isCafeOpen();
        Setting::setValue('cafe_is_open', $currentStatus ? '0' : '1');

        // Clear menu cache
        Cache::forget('menu_categories');

        $statusText = $currentStatus ? 'DITUTUP' : 'DIBUKA';
        return back()->with('success', "Cafe berhasil {$statusText}!");
    }
}
