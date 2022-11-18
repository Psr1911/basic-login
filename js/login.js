function login() {
	const username = document.getElementById('username').value;
	const password = document.getElementById('password').value;

	fetch('php/login.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({ username, password }),
	})
		.then(response => response.json())
		.then(data => {
			console.log(data);
			const token = data['token'];
			if (!token) {
				alert("Invalid username or password !");
				return;
			}
			localStorage.setItem("token", token);
			window.location = "/profile.html";
		})
		.catch(error => {
			console.error('Error:', error);
		});
}
