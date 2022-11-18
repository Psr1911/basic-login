function getProfile() {
	const token = localStorage.getItem('token');
	fetch('php/profile.php', {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'Authorization': token,
		},
	})
		.then(response => response.json())
		.then(data => {
			console.log(data);
			if (data['status'] === false) {
				window.location = '/login.html';
				return;
			}

			document.getElementById('age').value = data['age'];
			document.getElementById('dob').value = data['dob'];
			document.getElementById('phone').value = data['phone'];

		})
		.catch(error => {
		});
}

function updateProfile() {
	const token = localStorage.getItem('token');

	const age = document.getElementById('age').value;
	const dob = document.getElementById('dob').value;
	const phone = document.getElementById('phone').value;

	fetch('php/profile.php', {
		method: 'PUT',
		headers: {
			'Content-Type': 'application/json',
			'Authorization': token,
		},
		body: JSON.stringify({ age, dob, phone })
	})
		.then(response => response.json())
		.then(data => {
			console.log(data);
			if (data['status'] === false) {
				window.location = '/login.html';
				return;
			}
			alert("Update successful.");
		})
		.catch(error => {
			console.log(error);
		});
}

function logout() {
	localStorage.removeItem('token');
	window.location = '/login.html';
}

getProfile();