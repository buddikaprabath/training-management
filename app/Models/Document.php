<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'name',
        'status',
        'date_of_submitting',
        'training_id',
        'participant_id',
        'file_path' // Field to store the document
    ];
    protected $casts = [
        'training_id' => 'string', // Ensure training_id is handled as a string
        'participant_id' => 'string', //ensure participant_id is handled as a string
    ];

    // Define relationships
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    // Function to store file
    public function storeDocument(UploadedFile $file)
    {
        $path = $file->store('documents'); // Stores in storage/app/documents
        $this->update(['file_path' => $path]);
    }

    // Function to get file URL
    public function getDocumentUrl()
    {
        return Storage::url($this->file_path);
    }
}
