<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Something a visitor sent from a website form: contact message, volunteer or job
 * application, or donation pledge.
 */
class FormSubmission extends Model
{
    use HasUuids;

    /** type => [plural label, singular label, label of the "subject" column] */
    public const TYPES = [
        'job' => ['Job Applications', 'Job application', 'Position'],
        'contact' => ['Contact Messages', 'Contact message', 'Subject'],
        'donate' => ['Donations', 'Donation', 'Subject'],
        'volunteer' => ['Volunteer Applications', 'Volunteer application', 'Interest'],
    ];

    /** Status choices per type; the first one is given to new submissions. */
    public const STATUSES = [
        'job' => ['new' => 'New', 'reviewing' => 'Reviewing', 'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected', 'hired' => 'Hired'],
        'contact' => ['new' => 'New', 'replied' => 'Replied', 'closed' => 'Closed'],
        'donate' => ['new' => 'Pledged', 'contacted' => 'Contacted', 'received' => 'Received', 'cancelled' => 'Cancelled'],
        'volunteer' => ['new' => 'New', 'contacted' => 'Contacted', 'accepted' => 'Accepted', 'declined' => 'Declined'],
    ];

    protected $fillable = [
        'type', 'status', 'name', 'email', 'phone', 'subject', 'amount', 'message', 'details',
        'attachment_path', 'attachment_name', 'admin_note', 'locale', 'ip_address', 'email_sent',
    ];

    protected $casts = [
        'details' => 'array',
        'amount' => 'decimal:2',
        'email_sent' => 'boolean',
    ];

    public static function isType(?string $type): bool
    {
        return $type !== null && isset(self::TYPES[$type]);
    }

    public static function typeLabel(string $type, bool $plural = true): string
    {
        return self::TYPES[$type][$plural ? 0 : 1] ?? ucfirst($type);
    }

    public static function statusesFor(string $type): array
    {
        return self::STATUSES[$type] ?? ['new' => 'New'];
    }

    public function statusLabel(): string
    {
        return self::statusesFor($this->type)[$this->status] ?? ucfirst($this->status);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
