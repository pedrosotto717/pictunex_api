
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'keywords',
        'categories',
        'nickname',
        'src'
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'CREATION_DATE' => 'datetime',
        ];
    }

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nickname', 'username');
    }

    /**
     * Get the src attribute with full URL
     */
    public function getSrcAttribute($value)
    {
        return request()->getSchemeAndHttpHost() . $value;
    }

    /**
     * Scope for searching images
     */
    public function scopeSearch($query, $term)
    {
        return $query->whereRaw("MATCH(name, keywords, categories) AGAINST(? IN BOOLEAN MODE)", [$term])
                     ->orWhere('keywords', 'LIKE', "%{$term}%")
                     ->orWhere('name', 'LIKE', "%{$term}%");
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('categories', 'LIKE', "%{$category}%");
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $username)
    {
        return $query->where('nickname', $username);
    }
}
