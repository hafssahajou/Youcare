<?php

namespace App\Http\Controllers\Api;

use App\Models\Annoucements;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;





class AnnoucementsController extends Controller
{
    public function index()
    {
       $annoucements = Annoucements::all();
       if($annoucements->count() > 0){

        return response()->json([
            'status' =>200,
            'annoucements' =>$annoucements
        ], 200);
       }else{

        return response()->json([
            'status' => 404,
            'message_status' =>'NO Records Found'
        ],404);
       }
    }
    public function store(Request $request){
        
        $user = Auth::guard('api')->user();
       
        if (!$user || $user->role !== 'organizer') {
        return response()->json([
            'status' => false,
            'message' => 'Only organizers can create announcements'
        ], 403); 
    }
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'required_skills' => 'required|array'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);

        } else {

            $requiredSkillsString = implode(',', $request->required_skills);
            $announcement = new Annoucements();
            $announcement->title = $request->title;
            $announcement->type = $request->type;
            $announcement->date = $request->date;
            $announcement->description = $request->description;
            $announcement->location = $request->location;
            $announcement->required_skills = $requiredSkillsString;
            $announcement->organizer_id = $user->id;
            $announcement->save();

            if($announcement){
                return response()->json([
                    'status' => 200,
                    'message' => 'announcement created successfully',
                    'announcement' => $announcement

                ],200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'something went wrong'
                ],500);
            }
      }
 }
 public function show($id){
    $announcement = Annoucements::find($id);
    $user = Auth::guard('api')->user();
     if ($announcement){
         return response()->json([
             'status' => 200,
             'announcement' => $announcement
         ],200);
     } else {
         return response()->json([
             'status' => 404,
             'message' => 'no announcement found'
         ],404);
     }
 }

 public function edit($id){
    $user = Auth::guard('api')->user();
    $announcement = Annoucements::find($id);
    if ($announcement){
        return response()->json([
            'status' => 200,
            'message' => $announcement
        ],200);
    } else {
        return response()->json([
            'status' => 404,
            'message' => 'no annoucement found'
        ],404);
    }
}



public function update(Request $request, int $id){

    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'type' => 'required|string|max:255',
        'date' => 'required|date',
        'description' => 'required|string',
        'location' => 'required|string|max:255',
        'required_skills' => 'required|string'
    ]);
    
    if($validator->fails()){
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages()
        ], 422);
    } else {
        $announcement = Annoucements::find($id);
        
        if($announcement){
            $announcement->update([
                'title' => $request->title,
                'type' => $request->type,
                'date' => $request->date,
                'description' => $request->description,
                'location' => $request->location,
                'required_skills' => $request->required_skills,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Announcement updated successfully'
            ],200);
        }
        else {
            return response()->json([
                'status' => 404,
                'message' => 'No such announcement found'
            ],404);
        }
    }
}    

   
public function destroy($id){
    $announcement = Annoucements::find($id);
    if ($announcement){
        $announcement->delete();
        return response()->json([
            'status' => 200,
            'message' => 'announcement deleted successfully'
        ],404);
    }
    else {
        return response()->json([
            'status' => 404,
            'message' => 'no such announcement found'
        ],404);
    }

}
}