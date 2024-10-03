<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Méthode pour l'inscription
    public function register(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:12',
            'prenom' => 'required|string|max:60',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nom.required' => 'Le champ nom est requis.',
            'nom.string' => 'Le champ nom doit être une chaîne de caractères.',
            'nom.max' => 'Le champ nom ne doit pas dépasser 12 caractères.',

            'prenom.required' => 'Le champ prénom est requis.',
            'prenom.string' => 'Le champ prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le champ prénom ne doit pas dépasser 60 caractères.',

            'email.required' => 'Le champ email est requis.',
            'email.string' => 'Le champ email doit être une chaîne de caractères.',
            'email.email' => 'Le champ email doit être une adresse email valide.',
            'email.max' => 'Le champ email ne doit pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',

            'password.required' => 'Le champ mot de passe est requis.',
            'password.string' => 'Le champ mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Créer un nouvel utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Créer un token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    // Méthode pour la connexion
    public function login(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier les informations de l'utilisateur
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email ou mot de passe incorrecte!'], 401);
        }

        // Créer un token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user
        ]);
    }

    //Pour tester le logout aller dans header commme cle:Authorization et value: Bearer 2|codeTOken
    public function logout()
    {
        $user = auth()->user();

        // Vérifie si l'utilisateur est authentifié
        if (!$user) {
            // Retourne une réponse JSON avec un code d'état 401
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Supprime tous les tokens de l'utilisateur authentifié
        $user->tokens()->delete();

        // Retourne une réponse avec un code d'état 200 (OK)
        return response()->json(['message' => 'Déconnexion réussie avec succès'], 200);
    }

}
