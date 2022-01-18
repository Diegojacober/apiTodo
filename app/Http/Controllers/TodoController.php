<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Todo;

class TodoController extends Controller
{
    public function readAll()
    {

        $array = ['error' => ''];

        $todos = Todo::simplePaginate(2);

        $array['list'] = $todos->items();
        $array['current_page'] = $todos->currentPage();

        return json_encode($array);
    }

    public function create(Request $request)
    {

        $array = ['error' => ''];


        $rules = [
            'title' => 'required|min: 3'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $array['error'] = $validator->errors();
        }
        $title = $request->input('title');

        $todo = new Todo();
        $todo->title = $title;
        $todo->save();

        return json_encode($array);
    }

    public function readTodo($id)
    {
        $array = ['error' => ''];

        $todo = Todo::find($id);

        if ($todo) {
            $array['todo'] = $todo;
        } else {
            $array['error'] = 'A tarefa desejada é inexistente';
        }

        return json_encode($array);
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];

        //validando
        $rules = [
            'title' => 'min: 3',
            'done' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $array['error'] = $validator->errors();
        }
        $title = $request->input('title');
        $done = $request->input('done');

        $todo = Todo::find($id);
        if ($todo) {

            if ($title) {
                $todo->title = $title;
            }
            if ($done !== NULL) {
                $todo->done = $done;
            }

            $todo->save();
        } else {
            $array['error'] = 'Tarefa ' . $id . ' não existe';
        }
        $todo->title = $title;
        $todo->save();

        //atualizando

        return json_encode($array);
    }

    public function delete($id)
    {
        $array = ['error' => ''];

        $todo = Todo::find($id);
        $todo->delete();

        return json_encode($array);
    }
}
