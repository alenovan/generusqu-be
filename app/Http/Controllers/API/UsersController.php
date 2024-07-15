<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UsersRepository;
use Exception;

class UsersController extends Controller
{
    private $userRepository;

    public function __construct(UsersRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Store a newly created user with details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'password' => 'required',
                // Add validation rules for details if needed
            ]);

            $userData = [
                'username' => $request->username,
                'password' => $request->password,
            ];

            // Example of details data
            $detailsData = [
                'ig' => $request->ig ?? null,
                'email' => $request->email ?? null,
                'group_id' => $request->group_id ?? null,
                'datebirth' => $request->datebirth ?? null,
                'gender' => $request->gender ?? null,
                'class' => $request->class ?? null,
                'photo' => $request->photo ?? null, // Assuming base64 encoded image data
            ];

            $createdData = $this->userRepository->createUserWithDetails($userData, $detailsData);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $createdData,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified user with details.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = $this->userRepository->getUserWithDetails($id);

            if (!$user) {
                throw new Exception('User not found');
            }

            return response()->json([
                'message' => 'User found',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified user with details.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'username' => 'sometimes|required', 
                'password' => 'sometimes|required', 
            ]);

            $userData = [
                'username' => $request->username ?? null,
                'password' => $request->password ?? null,
            ];

            // Example of details data
            $detailsData = [
                'ig' => $request->ig ?? null,
                'email' => $request->email ?? null,
                'group_id' => $request->group_id ?? null,
                'datebirth' => $request->datebirth ?? null,
                'gender' => $request->gender ?? null,
                'class' => $request->class ?? null,
                'photo' => $request->photo ?? null, // Assuming base64 encoded image data
            ];

            $updatedData = $this->userRepository->updateUserWithDetails($id, $userData, $detailsData);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $updatedData,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified user with details.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->userRepository->deleteUserWithDetails($id);

            return response()->json([
                'message' => 'User deleted successfully',
                'deleted' => $deleted,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
