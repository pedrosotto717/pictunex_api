
<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ImageController extends Controller
{
    private $categories = [
        "animals", "scenery", "nature", "fantasy", "technology", 
        "science", "fashion", "architecture", "industry"
    ];

    /**
     * Display a listing of images
     */
    public function index(Request $request)
    {
        $query = Image::query()->groupBy('name')->orderBy('CREATION_DATE', 'desc');

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user = User::where('user_id', $request->user_id)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 400);
            }
            $query->byUser($user->username);
        }

        $images = $query->get();
        return response()->json($images);
    }

    /**
     * Display the specified image
     */
    public function show($id)
    {
        $image = Image::find($id);
        
        if (!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        return response()->json($image);
    }

    /**
     * Store a newly created image
     */
    public function store(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            $request->validate([
                'name' => 'required|string|max:30',
                'keywords' => 'required|string|max:80',
                'categories' => 'required|string|max:80',
                'src' => 'required|image|mimes:jpeg,png,jpg'
            ]);

            // Handle file upload
            $file = $request->file('src');
            $extension = $file->getClientOriginalExtension();
            $filename = str_replace(' ', '_', $request->name) . '_' . time() . '_pictunex.' . $extension;
            $path = $file->storeAs('public/img', $filename);

            $image = Image::create([
                'name' => $request->name,
                'keywords' => $request->keywords,
                'categories' => $request->categories,
                'nickname' => $user->username,
                'src' => '/storage/img/' . $filename
            ]);

            return response()->json(['message' => 'Image created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Update the specified image
     */
    public function update(Request $request, $id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $image = Image::where('id', $id)->where('nickname', $user->username)->first();

            if (!$image) {
                return response()->json(['error' => 'Image not found'], 404);
            }

            $updateData = [
                'name' => $request->name,
                'keywords' => $request->keywords,
                'categories' => $request->categories
            ];

            // Handle file upload if provided
            if ($request->hasFile('src')) {
                // Delete old file
                $oldPath = str_replace('/storage', 'public', $image->getRawOriginal('src'));
                Storage::delete($oldPath);

                $file = $request->file('src');
                $extension = $file->getClientOriginalExtension();
                $filename = str_replace(' ', '_', $request->name) . '_' . time() . '_pictunex.' . $extension;
                $path = $file->storeAs('public/img', $filename);
                $updateData['src'] = '/storage/img/' . $filename;
            }

            $image->update($updateData);

            return response()->json(['message' => 'Image updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Remove the specified image
     */
    public function destroy($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $image = Image::where('id', $id)->where('nickname', $user->username)->first();

            if (!$image) {
                return response()->json(['error' => 'Image not found'], 404);
            }

            // Delete file
            $filePath = str_replace('/storage', 'public', $image->getRawOriginal('src'));
            Storage::delete($filePath);

            $image->delete();

            return response()->json(['message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Get images by category
     */
    public function byCategory($category, Request $request)
    {
        if (!in_array($category, $this->categories)) {
            return response()->json(['error' => 'Invalid category'], 404);
        }

        $query = Image::byCategory($category)->groupBy('name')->orderBy('CREATION_DATE', 'desc');

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user = User::where('user_id', $request->user_id)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 400);
            }
            $query->byUser($user->username);
        }

        $images = $query->get();
        return response()->json($images);
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        return response()->json(['categories' => $this->categories]);
    }

    /**
     * Search images
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || !preg_match('/[a-z]/i', $query)) {
            return response()->json(['error' => 'Invalid search query'], 400);
        }

        $imagesQuery = Image::search($query)->groupBy('name')->orderBy('CREATION_DATE', 'desc');

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user = User::where('user_id', $request->user_id)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 400);
            }
            $imagesQuery->byUser($user->username);
        }

        $images = $imagesQuery->get();
        return response()->json($images);
    }
}
