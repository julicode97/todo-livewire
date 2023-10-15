<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task as TaskModel;

class Task extends Component
{
    use WithPagination;

    public TaskModel $task;
    public string $search = '';
    protected $rules = ['task.text' => 'required|max:40'];

    public function mount()
    {

        $this->task = new TaskModel();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTaskText()
    {
        $this->validate(['task.text' => 'max:40']);
    }

    public function edit(TaskModel $task)
    {
        $this->task = $task;
    }

    public function done(TaskModel $task)
    {
        $task->update(['done' => !$task->done]);
        $this->mount();
    }

    public function save()
    {
        $this->validate();
        $this->task->save();
        $this->mount();
        $this->emitUp('taskSaved', 'Tarea guardada correctamente!');
    }

    public function delete($id)
    {
        $taskToDelete = TaskModel::find($id);

        if(!is_null($taskToDelete)) {
            $taskToDelete->delete();
            $this->emitUp('taskSaved', 'Tarea eliminada correctamente!');
            $this->mount();
        }
    }

    public function render()
    {
        $tasks = TaskModel::latest()
            ->where('text', 'like', "%{$this->search}%")
            ->paginate(5);
        return view('livewire.task', ['tasks' => $tasks]);
    }
}
