<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;
    #[Rule('required|min:3|max:10')]
    public $name;

    public $search;


    public $editingTodoID;

    #[Rule('required|min:3|max:10')]
    public $editingTodoName;

    public function create()
    {

         $validated = $this->validate([
            'name' => 'required|min:3|max:10',
        ]);

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Saved successfully!');

        $this->resetPage();
    }

    public function delete($id)
    {
        Todo::find($id)->delete();
        session()->flash('success', 'Deleted successfully!');
    }

    public function toggle($id)
    {
        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($id)
    {
        $this->editingTodoID = $id;
        $this->editingTodoName = Todo::find($id)->name;
    }

    public function cancelEdit(){
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update() {
        $this->validateOnly('editingTodoName');

        Todo::find($this->editingTodoID)->update([
            'name' => $this->editingTodoName
        ]);

        $this->cancelEdit();
    }



    public function render()
    {

        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
