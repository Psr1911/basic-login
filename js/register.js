function register() {
	const username = document.getElementById('username').value;
	const password = document.getElementById('password').value;
	const confirm_password = document.getElementById('confirmPassword').value;

	fetch('php/register.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({ username, password, confirm_password }),
	})
		.then(response => response.json())
		.then(data => {
			console.error('Error:', data);
			const token = data['token'];
			if (!token) {
				if (data['message']) 
					alert(data['message']);
				return;
			}
			localStorage.setItem("token", token);
			window.location = "/profile.html";
		})
		.catch(error => {
			console.error('Error:', error);
		});
}
