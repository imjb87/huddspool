<?php

namespace App\Services;

use App\Models\User;
use daacreators\CreatorsTicketing\Models\Department;
use daacreators\CreatorsTicketing\Models\Form;
use daacreators\CreatorsTicketing\Models\FormField;
use daacreators\CreatorsTicketing\Models\Ticket;
use daacreators\CreatorsTicketing\Models\TicketReply;

class SupportTicketService
{
    /**
     * @return array{0: Department, 1: Form}
     */
    public function ensureFormExists(): array
    {
        $department = Department::firstOrCreate(
            ['slug' => 'support'],
            [
                'name' => 'Support',
                'description' => 'Support requests',
                'visibility' => 'public',
                'is_active' => true,
            ]
        );

        $form = Form::firstOrCreate(
            ['slug' => 'support-form'],
            [
                'name' => 'Support form',
                'description' => 'Simple support form',
                'is_active' => true,
            ]
        );

        $fields = [
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'is_required' => true,
                'order' => 1,
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'is_required' => true,
                'order' => 2,
            ],
            [
                'name' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'is_required' => true,
                'order' => 3,
            ],
        ];

        foreach ($fields as $field) {
            FormField::updateOrCreate(
                [
                    'form_id' => $form->id,
                    'name' => $field['name'],
                ],
                [
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'options' => null,
                    'is_required' => $field['is_required'],
                    'help_text' => null,
                    'validation_rules' => null,
                    'order' => $field['order'],
                ]
            );
        }

        $department->forms()->syncWithoutDetaching([$form->id]);

        return [$department, $form];
    }

    /**
     * @param  array{name: string, email: string, message: string}  $data
     */
    public function submit(User $requester, array $data): Ticket
    {
        [$department, $form] = $this->ensureFormExists();

        $ticket = Ticket::create([
            'department_id' => $department->id,
            'form_id' => $form->id,
            'custom_fields' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'message' => $data['message'],
            ],
            'user_id' => $requester->id,
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $requester->id,
            'content' => $data['message'],
            'is_internal_note' => false,
        ]);

        return $ticket;
    }
}
