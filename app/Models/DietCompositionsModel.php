<?php

namespace App\Models;

use CodeIgniter\Model;

class DietCompositionsModel extends Model
{
	protected $table = 'diet_compositions';

	protected $primaryKey = 'id';

	protected $allowedFields = [
		'recommendation_id',
		'diet_id',
		'food_item_id',
		'quantity'
	];
}
