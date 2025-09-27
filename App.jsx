import React from 'react'
import Hero from './components/Hero'
import ContactForm from './components/ContactForm'

export default function App(){
  return (
    <div>
      <Hero />
      <main className="container">
        <section>
          <h2>About Us</h2>
          <p>We help brands grow online. This is a starter marketing site.</p>
        </section>
        <ContactForm />
      </main>
      <footer className="footer">© {new Date().getFullYear()} MarketSite</footer>
    </div>
  )
}
