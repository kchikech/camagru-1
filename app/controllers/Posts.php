<?php

class Posts extends Controller {

	public function __construct() {
		if (!isLoggedIn())
			redirect('users/login');
		$this->postModel = $this->model('Post');
		$this->userModel = $this->model('User');
	}

	public function index() {
		$posts = $this->postModel->getPosts();
		$data = [
			'posts' => $posts
		];
		$this->view('posts/index', $data);
	}

	public function add() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			$data = [
				'super' => $_POST['super'],
				'user_id' => $_SESSION['user_id'],
				'x' => $_POST['x'],
				'y' => $_POST['y'],
				'image_err' => ''
			];
			if ($_POST['type']) {
				$data['image'] = $this->postModel->uploadImage($_FILES['image']);
			} else {
				$data['image'] = $this->postModel->saveImage64($_POST['imageData']);
			}
			watermark(dirname(dirname(APPROOT)).'/'.$data['image'], $data['super'], $data['x'], $data['y']);
			if (empty($data['image'])) {
				$data['image_err'] = 'Please upload an image';
			}
			if (empty($data['image_err'])) {
				if ($this->postModel->addPost($data)) {
					flash('post_message', 'Post Added');
					redirect('posts');
				} else {
					die('Ouups .. something went wrong !');
				}
			} else {
				$this->view('posts/add', $data);
			}
		} else {
			$data = [
				'title' => '',
				'body' => ''
			];
			$this->view('posts/add', $data);
		}
	}


	public function edit($id = -1) {
		if ($id == -1)
			redirect('posts');
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			$data = [
				'id' => $id,
				'title' => trim($_POST['title']),
				'body' => trim($_POST['body']),
				'user_id' => $_SESSION['user_id'],
				'title_err' => '',
				'body_err' => ''
			];
			if (empty($data['title'])) {
				$data['title_err'] = 'Please enter title';
			}
			if (empty($data['body'])) {
				$data['body_err'] = 'Please enter body text';
			}
			if (empty($data['title_err']) && empty($data['body_err'])) {
				if ($this->postModel->updatePost($data)) {
					flash('post_message', 'Post Updated');
					redirect('posts');
				} else {
					die('Ouups .. something went wrong !');
				}
			} else {
				$this->view('posts/edit', $data);
			}
		} else {
			$post = $this->postModel->getPostById($id);
			if ($post->user_id != $_SESSION['user_id'])
				redirect('posts');
			$data = [
				'id' => $id,
				'title' => $post->title,
				'body' => $post->body
			];
			$this->view('posts/edit', $data);
		}
	}

	public function show($id = -1) {
		if ($id == -1)
			redirect('posts');
		$post = $this->postModel->getPostById($id);
		$user = $this->userModel->getUserById($post->user_id);
		$data = [
			'post' => $post,
			'user' => $user
		];
		$this->view('posts/show', $data);
	}

	public function delete($id = -1) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $id !== -1) {
			$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			$post = $this->postModel->getPostById($id);
			if ($post->user_id != $_SESSION['user_id'])
				redirect('posts');
			if ($this->postModel->deletePost($id)) {
				flash('post_message', 'Post Removed');
				redirect('posts');
			} else {
				die('Ouups .. something went wrong !');
			}
		} else {
			redirect('posts');
		}
	}

}

?>