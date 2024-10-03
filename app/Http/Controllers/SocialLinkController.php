<?php

namespace App\Http\Controllers;

use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function store(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|max:12',
            'url' => 'nullable|url',
        ], [
            'platform.required' => 'La plateforme du service est requis.',
            'platform.max' => 'La plateforme ne doit pas dépasser 12 caractères.',
            'url.url' => 'haque lien social doit être une URL valide.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // Vérifier si l'utilisateur a un profil
        if ($user && $user->profile) {
            // Créer un nouveau service en associant le profil
            $service = SocialLink::create([
                'profile_id' => $user->profile->id, // Récupère l'ID du profil
                'platform' => $request->platform,
                'url' => $request->url,
            ]);

            return response()->json([
                'message' => 'Service ajouté avec succès.',
                'data' => $service,
            ], 201);
        }

        // Si l'utilisateur n'a pas de profil, retourner une erreur
        return response()->json(['message' => 'Profil non trouvé pour cet utilisateur.'], 404);
    }
}
