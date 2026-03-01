const navToggle = document.getElementById('navToggle')
const topNav = document.getElementById('topNav')
const themeToggle = document.getElementById('themeToggle')
const themeIcon = document.getElementById('themeIcon')
const themeText = document.getElementById('themeText')

if (navToggle && topNav) {
  navToggle.addEventListener('click', () => {
    topNav.classList.toggle('open')
  })
}

document.addEventListener('click', (e) => {
  if (!topNav) return
  const target = e.target
  const clickedToggle = target && (target.closest && target.closest('#navToggle'))
  const clickedNav = target && (target.closest && target.closest('#topNav'))
  if (!clickedToggle && !clickedNav) topNav.classList.remove('open')
})

const applyThemeUi = (t) => {
  if (!themeIcon || !themeText) return
  if (t === 'dark') {
    themeIcon.className = 'fa-solid fa-sun'
    themeText.textContent = 'Jasny'
  } else {
    themeIcon.className = 'fa-solid fa-moon'
    themeText.textContent = 'Ciemny'
  }
}

const getTheme = () => {
  const t = document.documentElement.dataset.theme
  if (t === 'dark' || t === 'light') return t
  return 'light'
}

applyThemeUi(getTheme())

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const next = getTheme() === 'dark' ? 'light' : 'dark'
    document.documentElement.dataset.theme = next
    try { localStorage.setItem('theme', next) } catch (e) {}
    applyThemeUi(next)
  })
}
