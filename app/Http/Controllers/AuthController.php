<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\InitPasswordByEmailRequest;
use App\Http\Requests\InitPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Notifications\PasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    /**
     * registers user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $token = $user->createToken(User::USER_TOKEN);
        return $this->success([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], "User has been registered successfully");
    }

    /**
     * registers user
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse {
        $data = $request->validated();
        $user = User::where("id",$data['user_id'])->with(['creator','roles'])->first();
        if(isset($user)){
            if($user->email == $data['email']){
                $dataUpdate = array_diff_key($data, array_flip(["user_id"]));
                $statusCreate = $user->update($dataUpdate);
                if(isset($statusCreate) && $statusCreate == true){
                    return $this->success([
                        "action_status" => $statusCreate,
                        "user" => $user,
                    ],"User updated successfully");
                }
                return $this->error("Error while updating user");
            }else{
                $userEmail = User::where('email', $data['email'])->first();
                if(isset($userEmail)){
                    return $this->error("Error email already in database. Choose another one");
                }else{
                    $dataUpdate = array_diff_key($data, array_flip(["user_id"]));
                    $statusCreate = $user->update($dataUpdate);
                    if(isset($statusCreate) && $statusCreate == true){
                        return $this->success([
                            "action_status" => $statusCreate,
                            "user" => $user,
                        ],"User updated successfully");
                    }
                    return $this->error("Error while updating user");
                }
            }
        }
        return $this->error("Error while getting user for update");
    }

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request) : JsonResponse{
        $isValid = $this->isValidCredential($request);
        if(!$isValid['success']){
            return $this->error($isValid['msg'], 200);
        }
        $user = $isValid["user"];
        $token = $user->createToken(User::USER_TOKEN);

        return $this->success([
            'user' => $user,
            'role' => $isValid["role"],
            'token' => $token->plainTextToken,
        ], "Login successfully");

    }


    /**
     * Validates user credentials
     *
     * @param  LoginRequest $request
     * @return array
     */
    private function isValidCredential(LoginRequest $request):array {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        $role = $user->getRoleNames()->first();
        if(!isset($user) || $user == null){
            return [
                'success' => false,
                'msg' => "No account attached to this email address : ".$data['email']
            ];
        }
        if(Hash::check($data['password'], $user->password)){
            return [
                'success' => true,
                'user' => $user,
                'role' => $role
            ];
        }
        return [
            'success' => false,
            'msg' => "Password is not matched"
        ];
    }

    /**
     * get list user to show to the current user
     *
     * @return JsonResponse
     */
    public function getListUserForAuthUser(): JsonResponse{
        $user = User::find(Auth()->user()->id);
        $usersWithoutRoles = null;
        if($user->hasRole(['Super-Admin', 'Responsable general'])){
            $excludedRoles = ['Super-Admin']; // Remplace par les rôles que tu veux exclure
            $usersWithoutRoles = User::whereDoesntHave('roles', function ($query) use ($excludedRoles) {
                                            $query->whereIn('name', $excludedRoles);
                                        })
                                        ->where('id','<>',$user->id)
                                        ->where("compagny_id", $user->compagny_id)
                                        ->with(['creator','roles'])
                                        ->get();
        }else{
            $excludedRoles = ['Super-Admin', 'Responsable general']; // Remplace par les rôles que tu veux exclure
            $usersWithoutRoles = User::whereDoesntHave('roles', function ($query) use ($excludedRoles) {
                                            $query->whereIn('name', $excludedRoles);
                                        })->where('id','<>',$user->id)
                                        ->with(['creator','roles'])
                                        ->where("compagny_id", $user->compagny_id)
                                        ->get();
        }

        if(isset($usersWithoutRoles)){
            return $this->success([
                'list_users' => $usersWithoutRoles
            ],"List users fetched successfully");
        }else{
            return $this->error("Error while fetching list users");
        }
    }

    /**
     * Logins a user with token
     *
     * @return JsonResponse
     */
    public function loginWithToken():JsonResponse {
        if(null !== (Auth()->user())){
            return $this->success(Auth()->user(), "Login successfully");
        }else{
            return $this->error("Error user unauthenticated");
        }
    }

    /**
     * logouts a user
     *
     * @param Request
     * @return JsonResponse
     */
    public function logout(Request $request):JsonResponse{
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, "Logout successfully");
    }


    /**
     * initializes password by user id
     *
     * @param ChangePasswordRequest
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request):JsonResponse{
        $data=$request->validated();
        $user = User::where('id', $data['user_id'])->with(['roles', 'creator'])->first();
        if(Hash::check($data['lastPassword'], $user->password)){
            $status = $user->update([
                'password' => Hash::make($data['password'])
            ]);
            if(isset($status) && $status == true){
                //TODO send email to notify the user
                Notification::send($user, new PasswordNotification($user, $data['password']));
                return $this->success([
                    'status' => $status
                ],"Password updated successfully");
            }else{
                return $this->error("Error unable to update the password");
            }
        }else{
            return $this->error("Error the last password is wrong");
        }
    }

    /**
     * initializes password by user id
     *
     * @param InitPasswordRequest $request
     * @return JsonResponse
     */
    public function initPassword(InitPasswordRequest $request):JsonResponse{
        $data=$request->validated();
        $notification = null;
        $user = User::where('id', $data['user_id'])->with(['roles', 'creator'])->first();
        $password = $this->genererMotDePasse(8);
        if(isset($password)){
            $status = $user->update([
                'password' => Hash::make($password)
            ]);
            if(isset($status) && $status == true){
                //TODO send email to notify the user
                $notification = Notification::send($user, new PasswordNotification($user, $password));
                Log::info("Name: ".$user->name." / password: ".$password);
                return $this->success([
                    'status' => $status,
                    'notification' => $notification
                ],"Password initialized successfully");
            }else{
                return $this->error("Error unable to init the password in database");
            }
        }else{
            return $this->error("Error unable to generate unknown password");
        }
    }


     /**
     * initializes password by email
     *
     * @param InitPasswordByEmailRequest $request
     * @return JsonResponse
     */
    public function initPasswordByEmail(InitPasswordByEmailRequest $request):JsonResponse{
        $data=$request->validated();
        $notification = null;
        $user = User::where('email', $data['email'])->with(['roles', 'creator'])->first();
        $password = $this->genererMotDePasse(8);
        if(isset($password) && isset($user)){
            $status = $user->update([
                'password' => Hash::make($password)
            ]);
            if(isset($status) && $status == true){
                //TODO send email to notify the user
                $notification = Notification::send($user, new PasswordNotification($user, $password));
                Log::info("Name: ".$user->name." / password: ".$password);
                return $this->success([
                    'status' => $status,
                    'notification' => $notification
                ],"Password initialized successfully");
            }else{
                return $this->error("Error unable to init the password in database");
            }
        }else{
            return $this->error("Error user not in database");
        }
    }

    /**
     *Generates unknown password every time

     *@param int $longueur
     *@return string
     */
    private function genererMotDePasse(int $longueur = 12): string {
        // Définir les caractères possibles pour le mot de passe
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        // Initialiser le mot de passe
        $motDePasse = '';
        // Générer le mot de passe
        for ($i = 0; $i < $longueur; $i++) {
            $index = rand(0, strlen($caracteres) - 1); // Choisir un index aléatoire
            $motDePasse .= $caracteres[$index]; // Ajouter le caractère choisi
        }

        return $motDePasse; // Retourner le mot de passe généré
    }

}
