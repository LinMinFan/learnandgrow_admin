<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ContactForm;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;

class ContactFormController extends Controller
{
    use RedirectWithFlashTrait;

    // 列表頁面
    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'form.index')) {
            return $response;
        };

        $contactForm = ContactForm::select([
            'id',
            'subject',
            'name',
            'email',
            'phone',
            'is_read',
            'created_at'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Form/Index', [
            'contactForm' => $contactForm
        ]);
    }

    // 刪除
    public function destroy(Request $request, $id)
    {
        $contactForm = ContactForm::findOrFail($id);

        $contactForm->delete();

        return response()->json(['message' => '表單刪除成功'], 200);
    }
}
