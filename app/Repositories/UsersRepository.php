<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UsersDetails;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalTrait;
use Intervention\Image\Facades\Image; // Import Intervention Image facade
class UsersRepository
{
    use GlobalTrait;

    /**
     * Create a new user with details.
     *
     * @param array $userData
     * @param array $detailsData
     * @return array
     * @throws Exception
     */
    public function createUserWithDetails(array $userData, array $detailsData)
    {
        DB::beginTransaction();

        try {
            // Create the user record
            $user = new User();
            $user->username = $userData['username'];
            $user->password = Hash::make($userData['password']);
            $user->save();

            // Create the user details record associated with the user
            $details = new UsersDetails();
            $details->user_id = $user->id; // Assumes 'id' is auto-incremented
            $details->ig = $detailsData['ig'] ?? null;
            $details->email = $detailsData['email'] ?? null;
            $details->group_id = $detailsData['group_id'] ?? null;
            $details->datebirth = $detailsData['datebirth'] ?? null;
            $details->gender = $detailsData['gender'] ?? null;
            $details->class = $detailsData['class'] ?? null;
            
              // Compress and save the photo
              if (isset($detailsData['photo'])) {
                $compressedImage = $this->compressAndSaveImage($detailsData['photo']);
                $details->photo = $compressedImage;
            }

            $details->save();

            DB::commit();

            return [
                'user' => $user,
                'details' => $details,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw new Exception($e);
        }
    }

    /**
     * Get all users with details.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsersWithDetails()
    {
        return User::with('details')->get();
    }

    /**
     * Get a user by ID with details.
     *
     * @param int $userId
     * @return Users|null
     */
    public function getUserWithDetails(int $userId)
    {
        return User::with('details')->find($userId);
    }

    /**
     * Update a user with details.
     *
     * @param int $userId
     * @param array $userData
     * @param array $detailsData
     * @return array
     * @throws Exception
     */
    public function updateUserWithDetails(int $userId, array $userData, array $detailsData)
    {
        DB::beginTransaction();

        try {
            // Update the user record
            $user = User::findOrFail($userId);
            $user->username = $userData['username'] ?? $user->username; // Ensure existing value if not provided
            if (isset($userData['password'])) {
                $user->password = Hash::make($userData['password']);
            }
            $user->save();

            // Update or create the user details record
            $details = UsersDetails::updateOrCreate(
                ['user_id' => $userId],
                [
                    'ig' => $detailsData['ig'] ?? null,
                    'email' => $detailsData['email'] ?? null,
                    'group' => $detailsData['group'] ?? null,
                    'datebirth' => $detailsData['datebirth'] ?? null,
                    'gender' => $detailsData['gender'] ?? null,
                    'class' => $detailsData['class'] ?? null,
                    'photo' => $detailsData['photo'] ?? null,
                ]
            );

            DB::commit();

            return [
                'user' => $user,
                'details' => $details,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw new Exception('Failed to update user and details');
        }
    }

    /**
     * Delete a user and their details.
     *
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function deleteUserWithDetails(int $userId)
    {
        DB::beginTransaction();

        try {
            // Delete user details
            UsersDetails::where('user_id', $userId)->delete();

            // Delete user
            User::destroy($userId);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw new Exception('Failed to delete user and details');
        }
    }

    private function compressAndSaveImage(string $imageData)
    {
        try {
            $image = Image::make($imageData);

            // Adjust image quality and save to temporary storage
            $image->encode('jpg', 80); // Example: compress to JPEG with 80% quality

            // Save to storage (adjust path as needed)
            $path = 'images/';
            $fileName = uniqid() . '.jpg'; // Example: generate unique filename
            $image->save(public_path($path . $fileName));

            return $path . $fileName; // Return path to the compressed image
        } catch (\Exception $e) {
            report($e);
            throw new Exception('Failed to compress and save image');
        }
    }
}
