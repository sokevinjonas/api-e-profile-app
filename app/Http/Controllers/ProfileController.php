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
            'bio' => 'nullable|string|max:50',
        ], [
            'bio.max' => 'La bio ne doit pas dépasser 50 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Récupérer l'utilisateur authentifié
        $user = $request->user();

        // Vérifier si un profil existe déjà pour cet utilisateur
        if ($user->profile) {
            return response()->json([
                'message' => 'Un profil existe déjà pour cet utilisateur.',
            ], 409); // 409 Conflict
        }

        // Créer le profil si aucun n'existe
        $profile = Profile::create([
            'user_id' => $user->id,
            'bio' => $request->bio,
        ]);

        return response()->json([
            'data' => $profile,
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

    public function update(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'bio' => 'nullable|string|max:500',
            'social_links' => 'nullable|array',
            'social_links.*.id' => 'nullable|exists:social_links,id',
            'social_links.*.platform' => 'required|string|max:50',
            'social_links.*.url' => 'required|url',
            'services' => 'nullable|array',
            'services.*.id' => 'nullable|exists:services,id',
            'services.*.titre' => 'required|string|max:100',
            'services.*.description' => 'nullable|string|max:500',
            'services.*.price' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Récupérer le profil de l'utilisateur connecté
        $profile = auth()->user()->profile;

        // Vérifier si le profil existe
        if (!$profile) {
            return response()->json(['message' => 'Profil non trouvé.'], 404);
        }

        // Mettre à jour le profil
        $profile->bio = $request->bio;
        $profile->save();

        // Mettre à jour les liens sociaux
        if ($request->social_links) {
            foreach ($request->social_links as $link) {
                if (isset($link['id'])) {
                    // Mettre à jour le lien social existant
                    $socialLink = SocialLink::find($link['id']);
                    if ($socialLink) {
                        $socialLink->platform = $link['platform'];
                        $socialLink->url = $link['url'];
                        $socialLink->save();
                    }
                } else {
                    // Ajouter un nouveau lien social
                    SocialLink::create([
                        'profile_id' => $profile->id,
                        'platform' => $link['platform'],
                        'url' => $link['url'],
                    ]);
                }
            }
        }

        // Mettre à jour les services
        if ($request->services) {
            foreach ($request->services as $service) {
                if (isset($service['id'])) {
                    // Mettre à jour le service existant
                    $existingService = Service::find($service['id']);
                    if ($existingService) {
                        $existingService->titre = $service['titre'];
                        $existingService->description = $service['description'];
                        $existingService->price = $service['price'];
                        $existingService->save();
                    }
                } else {
                    // Ajouter un nouveau service
                    Service::create([
                        'profile_id' => $profile->id,
                        'titre' => $service['titre'],
                        'price' => $service['price'],
                    ]);
                }
            }
        }

        // Retourner le profil mis à jour avec ses relations
        return response()->json(['data' => $profile->load(['socialLinks', 'services'])], 200);
    }


    public function destroy()
    {
        // Récupérer le profil de l'utilisateur connecté
        $profile = auth()->user()->profile;

        // Vérifier si le profil existe
        if (!$profile) {
            return response()->json(['message' => 'Profil non trouvé.'], 404);
        }

        // Supprimer le profil (cela supprimera aussi les liens sociaux et services grâce à la contrainte `onDelete('cascade')`)
        $profile->delete();

        // Retourner une réponse de succès
        return response()->json(['message' => 'Profil supprimé avec succès.'], 200);
    }

}
