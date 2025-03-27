function toggleTrace(element) {
	const trace = element.nextElementSibling;
	element.classList.toggle('collapsed');
	trace.classList.toggle('show');
}

// Collapse all traces by default
document.querySelectorAll('.trace-toggle').forEach(toggle => {
	toggle.classList.add('collapsed');
});