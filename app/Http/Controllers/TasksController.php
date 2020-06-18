<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    

class TasksController extends Controller
{
    /**
     * リソースのリストを表示します。
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
      //未ログインだとwelcomeページに遷移
      if(!\Auth::check()){
          return view('welcome');
      }
      
      //$tasks = Task::all();
      $tasks = Task::where('user_id', \Auth()->id())->get();
      
      return view('tasks.index', [
        'tasks' => $tasks,
    ]);
}    

        

    /**
     * 新しいリソースを作成するためのフォームを表示します。
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * 新しく作成したリソースをストレージに保存します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $this->validate($request, [
            'content' => "required|max:191",
            'status' => "required|max:10", 
        ]);
        
        
        $task = new Task;
        $task->status = $request->status;
        $task->content = $request->content;
        $task->user_id = \Auth()->id();
        $task->save();

        return redirect('/');
    }
    
    /**
     * 指定したリソースを表示します。
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
    {
        $task = Task::findOrFail($id);
        
        // 所有者以外はトップページにリダイレクト
        if(\Auth::user()->id != $task->user_id){
            return redirect('/');
        }

        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    /**
     * 指定されたリソースを編集するためのフォームを表示します。
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function edit($id)
    {
        $task = Task::findOrFail($id);

        // 所有者以外はトップページにリダイレクト
        if(\Auth::user()->id != $task->user_id){
            return redirect('/');
        }
        
        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    /**
     * ステレージ内の指定されたリソースを更新します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
    {
         $this->validate($request, [
            'content' => "required|max:191",
            'status' => "required|max:10", 
        ]);
        
        $task = Task::findOrFail($id);
        
        if (\Auth::id() == $task->user_id) {
            $task->status = $request->status;
            $task->content = $request->content;
            // タスクの更新前後で所有者は変わらないのでコメントアウト
            // $task->user_id = \Auth()->id();
            $task->save();
        }

        return redirect('/');
    }

    /**
     * 指定されたリソースをストレージから削除します。
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id)
    {
        $task = Task::findOrFail($id);
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }


        return redirect('/');
    }
}
