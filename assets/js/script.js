(function() {
	const nav = document.querySelectorAll('main h2');
	const toc = document.querySelector('.sidebar .toc');

	if(nav && toc) {
		for(let item of nav) {
			node = document.createElement('a');
			node.setAttribute('href', '#' + item.getAttribute('id'));
			node.appendChild(document.createTextNode(item.textContent));
			toc.appendChild(node);
		}
	}

	const open = document.querySelector('.open');
	const close = document.querySelector('.close');
	const sidebar = document.querySelector('.sidebar');
	const content = document.querySelector('.main-content');

	open.addEventListener('click', function() {
		open.classList.toggle('show');
		close.classList.toggle('show');
		sidebar.classList.toggle('show');
	});

	close.addEventListener('click', function() {
		sidebar.classList.toggle('show');
		close.classList.toggle('show');
		open.classList.toggle('show');
	});

	const fcn = function() {
		sidebar.classList.remove('show');
		content.classList.remove('show');
		close.classList.remove('show');
		open.classList.add('show');
	};

	toc.addEventListener('click', fcn);
	content.addEventListener('click', fcn);
})();
