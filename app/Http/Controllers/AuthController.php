<?php

namespace App\Http\Controllers;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
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
            'prenom.required' => 'Le champ prénom est requis.',
            'email.required' => 'Le champ email est requis.',
            'password.required' => 'Le champ mot de passe est requis.',
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

        // Retourner une réponse de succès sans générer de token
        return response()->json([
            'message' => 'Inscription réussie. Veuillez vous connecter pour accéder à votre compte.',
            'user' => $user,
        ], 201);
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
            return response()->json(['message' => 'Email ou mot de passe incorrect!'], 401);
        }

        // Créer un JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user
        ]);
    }

    //Pour tester le logout aller dans header commme cle:Authorization et value: Bearer 2|codeTOken
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Déconnexion réussie']);
    }

}
