<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetExceededNotification extends Notification
{
    use Queueable;

    protected $category;
    protected $amountSpent;
    protected $budgetLimit;

    public function __construct($category, $amountSpent, $budgetLimit)
    {
        $this->category = $category;
        $this->amountSpent = $amountSpent;
        $this->budgetLimit = $budgetLimit;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Budget Exceeded Alert!')
                    ->greeting('Hello,')
                    ->line("Your budget for '{$this->category}' has been exceeded.")
                    ->line("Budget Limit: ₹{$this->budgetLimit}")
                    ->line("Total Spent: ₹{$this->amountSpent}")
                    ->line("Please review your expenses.")
                    ->action('View Expenses', url('/expenses'))
                    ->line('Thank you for using Expense Manager!');
    }
}
