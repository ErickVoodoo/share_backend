<?php

use App\User;
use App\Plan;
use App\Role;
use App\RoleUser;
use App\Deliver;
use App\Category;
use App\Location;
use App\Permission;
use App\PermissionRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $planId = Plan::create(['id' => Uuid::uuid4()->toString(), 'cost' => '0', 'period' => '99999', 'name' => 'Free plan']);

        $users = array(
            [
              'id' => Uuid::uuid4()->toString(),
              'name' => 'admin',
              'login' => 'admin',
              'email' => 'admin@admin.com',
              'password' => bcrypt('admin'),
              'plan_id' => $planId->id,
            ],
            [
              'id' => Uuid::uuid4()->toString(),
              'name' => 'Alexandro Chekville',
              'login' => 'alex.chekville',
              'email' => 'alex.chekville@gmail.com',
              'password' => bcrypt('secret'),
              'plan_id' => $planId->id,
            ],
        );

        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
        }

        $roles = array(
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'admin',
            'display_name' => 'admin',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'publisher',
            'display_name' => 'publisher',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'guest',
            'display_name' => 'guest',
          ],
        );

        foreach ($roles as $role)
        {
          Role::create($role);
        }

        $permissions = array(
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'delete-products',
            'display_name' => 'delete-products',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'create-products',
            'display_name' => 'create-products',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'update-products',
            'display_name' => 'update-products',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'update-users',
            'display_name' => 'update-users',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'delete-users',
            'display_name' => 'delete-users',
          ],
        );

        foreach ($permissions as $permission)
        {
          Permission::create($permission);
        }

        $role_users = array(
          [
            'user_id' => $users[0]['id'],
            'role_id' => $roles[0]['id'],
          ],
          [
            'user_id' => $users[1]['id'],
            'role_id' => $roles[1]['id'],
          ],
        );

        foreach ($role_users as $role_user)
        {
          RoleUser::create($role_user);
        }

        $permission_roles = array(
          [
            'permission_id' => $permissions[0]['id'],
            'role_id' => $roles[0]['id'],
          ],
          [
            'permission_id' => $permissions[0]['id'],
            'role_id' => $roles[1]['id'],
          ],
          [
            'permission_id' => $permissions[1]['id'],
            'role_id' => $roles[1]['id'],
          ],
          [
            'permission_id' => $permissions[2]['id'],
            'role_id' => $roles[0]['id'],
          ],
          [
            'permission_id' => $permissions[2]['id'],
            'role_id' => $roles[1]['id'],
          ],
          [
            'permission_id' => $permissions[3]['id'],
            'role_id' => $roles[0]['id'],
          ],
          [
            'permission_id' => $permissions[3]['id'],
            'role_id' => $roles[1]['id'],
          ],
          [
            'permission_id' => $permissions[4]['id'],
            'role_id' => $roles[0]['id'],
          ],
        );

        foreach ($permission_roles as $permission_role)
        {
          PermissionRole::create($permission_role);
        }

        $delivers = array(
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Самовывоз',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'По почте',
          ],
        );

        foreach ($delivers as $deliver)
        {
          Deliver::create($deliver);
        }

        $categories = array(
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Пища',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'name' => 'Одежда',
          ],
        );

        foreach ($categories as $category)
        {
          Category::create($category);
        }

        $locations = array(
          [
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $users[1]['id'],
            'city' => 'Москва',
            'street' => 'Притыцкого 43',
            'lat' => '59.33',
            'lon' => '58.22',
          ],
          [
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $users[1]['id'],
            'city' => 'Санкт - Петербург',
            'street' => 'Грибоедова 19',
            'lat' => '57.13',
            'lon' => '59.72',
          ],
        );

        foreach ($locations as $location)
        {
          Location::create($location);
        }

        Model::reguard();
    }
}
