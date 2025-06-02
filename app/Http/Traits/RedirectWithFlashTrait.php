<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait RedirectWithFlashTrait
{
    public function redirectIfHasFlashParams($request, $routeName, $params = [])
    {
        $status = null;
        $message = null;

        if ($request->has('success')) {
            $status = 'success';
        } elseif ($request->has('error')) {
            $status = 'error';
        }

        if (!empty($status)) {
            $message = $request->get($status);
            
            return redirect()->route($routeName, $params)->with([$status => $message]);
        }
    }
}
