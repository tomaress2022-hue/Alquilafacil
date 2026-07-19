<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** GET /api/profile */
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    /** PUT/PATCH /api/profile */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'documento' => ['nullable', 'string', 'max:20'],
            'phone'     => ['nullable', 'string', 'max:20'],
        ]);

        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        $user->fill($validated);
        $user->save();

        return new UserResource($user);
    }

    /** DELETE /api/profile */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Revoca todos los tokens de acceso del usuario antes de borrarlo
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Cuenta eliminada correctamente.']);
    }
}
