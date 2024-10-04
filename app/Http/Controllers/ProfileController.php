<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Service;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
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

        // Vérifier si un profil existe pour cet utilisateur
        $profile = $user->profile;

        if (!$profile) {
            return response()->json([
                'message' => 'Profil non trouvé.',
            ], 404);
        }

        // Mettre à jour les champs du profil
        $profile->update([
            'bio' => $request->bio,
        ]);

        return response()->json([
            'message' => 'Le profil a été mis à jour',
            'data' => $profile, // Renvoie les données du profil mis à jour
        ], 200);
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
