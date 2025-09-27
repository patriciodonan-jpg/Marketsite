\
import React, { useState } from "react";
import axios from "axios";

export default function ContactForm() {
  const [form, setForm] = useState({ name: "", email: "", message: "" });
  const [status, setStatus] = useState("");

  const backendUrl = import.meta.env.VITE_API_URL || "http://localhost:8080/api/contact.php";

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setStatus("Sending...");
    try {
      const resp = await axios.post(backendUrl, form, {
        headers: { "Content-Type": "application/json" },
      });
      if (resp.data && resp.data.success) {
        setStatus("Thanks! We'll be in touch.");
        setForm({ name: "", email: "", message: "" });
      } else {
        setStatus("Submission failed. Try again.");
      }
    } catch (err) {
      console.error(err);
      setStatus("Error sending submission.");
    }
  }

  return (
    <section id="contact">
      <h3>Contact Us</h3>
      <form onSubmit={handleSubmit} className="form">
        <label>
          Name
          <input name="name" value={form.name} onChange={handleChange} required />
        </label>
        <label>
          Email
          <input name="email" type="email" value={form.email} onChange={handleChange} required />
        </label>
        <label>
          Message
          <textarea name="message" value={form.message} onChange={handleChange} required />
        </label>
        <button type="submit">Send</button>
      </form>
      <p className="status">{status}</p>
    </section>
  );
}
