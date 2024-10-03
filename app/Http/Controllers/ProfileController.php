<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Service;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'bio' => 'nullable|string|max:500',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required|string|max:50',
            'social_links.*.url' => 'required|url',
            'services' => 'nullable|array',
            'services.*.titre' => 'required|string|max:100',
            'services.*.description' => 'nullable|string|max:500',
            'services.*.price' => 'nullable|integer',
        ], [
            'bio.max' => 'La bio ne doit pas dépasser 500 caractères.',
            'social_links.array' => 'Les liens sociaux doivent être un tableau.',
            'social_links.*.platform.required' => 'La plateforme est requise.',
            'social_links.*.platform.max' => 'La plateforme ne doit pas dépasser 50 caractères.',
            'social_links.*.url.required' => 'L\'URL est requise.',
            'social_links.*.url.url' => 'Chaque lien social doit être une URL valide.',
            'services.array' => 'Les services doivent être un tableau.',
            'services.*.titre.required' => 'Le titre du service est requis.',
            'services.*.titre.max' => 'Le titre du service ne doit pas dépasser 100 caractères.',
            'services.*.description.max' => 'La description ne doit pas dépasser 500 caractères.',
            'services.*.price.integer' => 'Le prix doit être un entier.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Récupérer l'ID de l'utilisateur authentifié
        $userId = $request->user()->id;

        // Créer le profil
        $profile = Profile::create([
            'user_id' => $userId,
            'bio' => $request->bio,
        ]);

        // Enregistrer les liens sociaux
        if ($request->social_links) {
            // Vérifie la présence et l'existence de données pour social_links dans la requête
            foreach ($request->social_links as $link) {
                SocialLink::create([
                    'profile_id' => $profile->id,
                    'platform' => $link['platform'],
                    'url' => $link['url'],
                ]);
            }
        }

        // Enregistrer les services
        if ($request->services) {
            // Vérifie la présence et l'existence de données pour services dans la requête
            foreach ($request->services as $service) {
                Service::create([
                    'profile_id' => $profile->id,
                    'titre' => $service['titre'],
                    'description' => $service['description'],
                    'price' => $service['price'],
                ]);
            }
        }

        return response()->json([
            // load(['socialLinks', 'services']) : Charge les relations de socialLinks et services pour un modèle donné, évitant des requêtes supplémentaires.
            'data' => $profile->load(['socialLinks', 'services'])
        ], 201);
    }

    public function show()
    {
        // Récupérer le profil de l'utilisateur connecté
        $profile = auth()->user()->profile()->with(['socialLinks', 'services'])->first();

        // Vérifier si le profil existe
        if (!$profile) {
            return response()->json(['message' => 'Profil non trouvé.'], 404);
        }

        // Retourner le profil avec ses relations
        return response()->json(['data' => $profile], 200);
    }


}
