<?php

namespace App\Http\Controllers;

use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialLinkController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user && $user->profile) {
            $data = SocialLink::where('profile_id', $user->profile->id)->get();
        }
        return response()->json([
            'message' => 'Reseaux Sociaux récuperé avec succès.',
            'data' => $data,
        ], 201);
    }

    public function store(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|max:12|unique:social_links,platform,NULL,id,profile_id,' . auth()->user()->profile->id,  // Validation unique basée sur profile_id
            'url' => 'nullable|url',
        ], [
            'platform.required' => 'La plateforme est requise.',
            'platform.max' => 'La plateforme ne doit pas dépasser 12 caractères.',
            'platform.unique' => 'Cette plateforme existe déjà pour ce profil.',  // Message d'erreur personnalisé
            'url.url' => 'Chaque lien social doit être une URL valide.',
        ]);

        // Vérification des erreurs de validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // Vérifier si l'utilisateur a un profil
        if ($user && $user->profile) {
            // Créer un nouveau service en associant le profil
            $service = SocialLink::create([
                'profile_id' => $user->profile->id,  // Récupère l'ID du profil
                'platform' => $request->platform,
                'url' => $request->url,
            ]);

            return response()->json([
                'message' => 'Reseau Social ajouté avec succès.',
                'data' => $service,
            ], 201);
        }

        // Si l'utilisateur n'a pas de profil, retourner une erreur
        return response()->json(['message' => 'Profil non trouvé pour cet utilisateur.'], 404);
    }
}
