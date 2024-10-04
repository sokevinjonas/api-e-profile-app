<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user && $user->profile) {
            $data = Service::where('profile_id', $user->profile->id)->get();
        }
        return response()->json([
            'message' => 'Service récuperé avec succès.',
            'data' => $data,
        ], 201);
    }

    public function store(Request $request)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:50',
            'price' => 'nullable|integer',
        ], [
            'titre.required' => 'Le titre du service est requis.',
            'titre.max' => 'Le titre du service ne doit pas dépasser 50 caractères.',
            'price.integer' => 'Le prix doit être un entier.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // Vérifier si l'utilisateur a un profil
        if ($user && $user->profile) {
            // Créer un nouveau service en associant le profil
            $service = Service::create([
                'profile_id' => $user->profile->id, // Récupère l'ID du profil
                'titre' => $request->titre,
                'price' => $request->price,
            ]);

            return response()->json([
                'message' => 'Service ajouté avec succès.',
                'data' => $service,
            ], 201);
        }

        // Si l'utilisateur n'a pas de profil, retourner une erreur
        return response()->json(['message' => 'Profil non trouvé pour cet utilisateur.'], 404);
    }

    public function update(Request $request, $id)
    {
        // Validation des champs
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:50',
            'price' => 'nullable|integer',
        ], [
            'titre.required' => 'Le titre du service est requis.',
            'titre.max' => 'Le titre du service ne doit pas dépasser 50 caractères.',
            'price.integer' => 'Le prix doit être un entier.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Récupérer l'utilisateur authentifié
        $user = auth()->user();

        // Vérifier si l'utilisateur a un profil
        if ($user && $user->profile) {
            // Trouver le service par ID
            $service = Service::where('id', $id)->where('profile_id', $user->profile->id)->first();

            // Vérifier si le service existe
            if (!$service) {
                return response()->json(['message' => 'Service non trouvé.'], 404);
            }

            // Mettre à jour les informations du service
            $service->titre = $request->titre;
            $service->price = $request->price;
            $service->save(); // Enregistrer les modifications

            return response()->json([
                'message' => 'Service mis à jour avec succès.',
                'data' => $service,
            ], 200);
        }

        // Si l'utilisateur n'a pas de profil, retourner une erreur
        return response()->json(['message' => 'Profil non trouvé pour cet utilisateur.'], 404);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'message' => 'Service non trouvé.'
            ], 404);
        }
        $service->delete();

        return response()->json([
            'message' => 'Service supprimé avec succès.'
        ], 200);

    }

}
