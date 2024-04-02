<?php

namespace Tests\Feature;

use App\Models\Annoucements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnoucementsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all announcements.
     */
    public function test_get_all_announcements()
    {
        
        // Créer quelques annonces pour les tester
        Annoucements::factory()->count(3)->create();

        // Faire une requête pour récupérer toutes les annonces
        $response = $this->get('/api/announcements');

        // Vérifier que la réponse est un succès (code 200)
        $response->assertStatus(200);

        // Vérifier que le JSON de réponse contient les données attendues
        $response->assertJsonStructure([
            'status',
            'announcements',
        ]);
    }

    /**
     * Test creating a new announcement.
     */
    public function test_create_announcement()
    {
        // Créer un utilisateur organisateur (mock)
        $user = \App\Models\User::factory()->create(['role' => 'organizer']);

        // Authentifier l'utilisateur
        $this->actingAs($user, 'api');

        // Données d'annonce à utiliser dans la requête
        $announcementData = [
            'title' => 'New Announcement',
            'type' => 'Event',
            'date' => '2024-04-01',
            'description' => 'This is a new announcement.',
            'location' => 'Somewhere',
            'required_skills' => ['Skill1', 'Skill2'],
        ];

        // Faire une requête pour créer une nouvelle annonce
        $response = $this->post('/api/announcements', $announcementData);

        // Vérifier que la réponse est un succès (code 200)
        $response->assertStatus(200);

        // Vérifier que le JSON de réponse contient les données attendues
        $response->assertJson([
            'status' => 200,
            'message' => 'announcement created successfully',
            'announcement' => $announcementData,
        ]);
    }

    /**
     * Test deleting an announcement.
     */
    public function test_delete_announcement()
    {
        // Créer une annonce pour la supprimer ensuite
        $announcement = Annoucements::factory()->create();

        // Faire une requête pour supprimer l'annonce
        $response = $this->delete('/api/announcements/' . $announcement->id);

        // Vérifier que la réponse est un succès (code 200)
        $response->assertStatus(200);

        // Vérifier que l'annonce a été supprimée de la base de données
        $this->assertDeleted($announcement);
    }
}
