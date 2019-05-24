<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use Entrust;

class UserRoleScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // return $builder->where('id', '=', 1);
        // if(Entrust::hasRole(['contractor'])){
        //   return $builder->where('added_by','=',Auth::id());
        // }
        return $builder->where('id', '=', Auth::id());
    }
}