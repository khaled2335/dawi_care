<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;
use Auth;
class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(
            [
                'name'=>'required',
                'email'=>'required |email |unique:users',
                'password'=>'required|min:6|max:20',
            ]);
          
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $res = $user->save();
        if ($res) {
            return response()->json(['message'=>'regestration successfully' ,'user'=>$user]);
         }
         else {
             return response()->json(['message'=>'regestration fail']);
         }
    }

    public function login(Request $request)
    {
        $request->validate(
            [
           
                'email'=>'required |email',
                'password'=>'required',
            ]);
            $credintail = $request->only('email' ,'password');
            $token=Auth::guard('api')->attempt($credintail);
            if (!$token) {
                return response()->json('error'); 
            }
             $user = Auth::guard('api')->user(); 
             return response()->json( ['token'=>$token ,'data'=> $user]);     
        }

        public function logout()
        {
            auth()->logout();
             return response()->json(' you loged out');     
        }
        public function profile()
        {
             return response()->json(auth()->user());     
        }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 
            $users = User::all();  
            return response()->json($users);
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 
           
        $request->validate(
            [
                'name'=>'required',
                'email'=>'required |email |unique:users',
                'password'=>'required|min:6|max:20',
            ]);
          
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // $user->role = $request->role;
        $user->role = 'admin';
        $res = $user->save();
        if ($res) {
            return response()->json(['message'=>'User added successfully']);
         }
         else {
             return response()->json(['message'=>'fail']);
         }
        }
        return response()->json(['message' => 'Unauthorized'], 403);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([ $user ]);
        }
        else {
            return response()->json(['message'=>'user not found']);
        }
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id,Request $request)
    {
        $request->validate(
            [
                'name'=>'required',
                'email'=>'required |email |unique:users',
                'password'=>'required|min:6|max:20',
            ]);
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') {
           $user = User::find($id);
           $user->name = $request->name;
           $user->password = Hash::make($request->password);
           $user->email = $request->email;
           $res = $user->save();
           if ($res) {
              return response()->json(['massege'=>'updated successfully' , $user  ]);
           }
           return response()->json(['massege'=>'fail']);
   
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') {
        $user = User::find($id);
        if($user){
         $user->delete();
         return response()->json(['massege'=>'user deleted successfully']);
        }
        return response()->json([ 'message' => 'user not found (id is wrong)']);
    }
       return response()->json(['message' => 'Unauthorized'], 403); 
           
      
}





























}