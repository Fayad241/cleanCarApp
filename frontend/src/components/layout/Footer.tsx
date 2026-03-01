import { FaFacebookF, FaInstagram, FaTiktok } from "react-icons/fa";

export function Footer() {
  return (
    <footer className="bg-[var(--bg-primary)] border-t border-[var(--border-subtle)] py-14 px-6">
      <div className="max-w-7xl mx-auto">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-10 mb-16">

          {/* Brand */}
          <div className="md:col-span-1">
            <div className="flex items-center gap-3 mb-5">
              <div className="w-9 h-9 rounded-full bg-[var(--green-primary)] flex items-center justify-center shadow-lg shadow-[var(--gold-glow)]">
                <span className="text-[var(--bg-primary)] font-black text-xs">CC</span>
              </div>
              <span className="text-[var(--text-primary)] font-light tracking-[0.2em] text-base uppercase">
                Clean<span className="text-[var(--green-primary)] font-semibold">Car</span>
              </span>
            </div>
            <p className="text-[var(--text-muted)] text-sm leading-relaxed max-w-xs mb-6">
              Station de lavage automobile premium à Cotonou. L'excellence à chaque passage.
            </p>
            {/* Réseaux sociaux */}
            <div className="flex items-center gap-3">
              
              <a
                href=""
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full border border-[var(--border-subtle)] 
                          flex items-center justify-center 
                          transition-all duration-300 
                          hover:bg-[var(--gold-glow)] 
                          hover:border-[var(--green-primary)] 
                          hover:scale-105"
              >
                <FaFacebookF className="text-[var(--gold-deep)] text-sm hover:text-[var(--green-primary)]" />
              </a>

              <a
                href=""
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full border border-[var(--border-subtle)] 
                          flex items-center justify-center 
                          transition-all duration-300 
                          hover:bg-[var(--gold-glow)] 
                          hover:border-[var(--green-primary)] 
                          hover:scale-105"
              >
                <FaInstagram className="text-[var(--gold-deep)] text-sm hover:text-[var(--green-primary)]" />
              </a>

              <a
                href=""
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full border border-[var(--border-subtle)] 
                          flex items-center justify-center 
                          transition-all duration-300 
                          hover:bg-[var(--gold-glow)] 
                          hover:border-[var(--green-primary)] 
                          hover:scale-105"
              >
                <FaTiktok className="text-[var(--gold-deep)] text-sm hover:text-[var(--green-primary)]" />
              </a>

            </div>
          </div>

          {/* Liens rapides */}
          <div>
            <p className="text-[var(--green-primary)] text-xs tracking-[0.2em] uppercase mb-5 font-medium">Navigation</p>
            <div className="flex flex-col gap-4">
              {["Accueil", "Services", "Tarifs", "À propos"].map((l) => (
                <a key={l} href="#" className="text-[var(--text-muted)] hover:text-[var(--green-primary)] text-sm tracking-wide transition-colors duration-[var(--transition-base)]">
                  {l}
                </a>
              ))}
            </div>
          </div>

          {/* Support */}
          {/* <div>
            <p className="text-[var(--text-faint)] text-xs tracking-[0.2em] uppercase mb-5 font-medium">Support</p>
            <div className="flex flex-col gap-4">
              {["FAQ", "Contact", "Mentions légales", "CGU"].map((l) => (
                <a key={l} href="#" className="text-[var(--text-muted)] hover:text-[var(--green-primary)] text-sm tracking-wide transition-colors duration-[var(--transition-base)]">
                  {l}
                </a>
              ))}
            </div>
          </div> */}

          {/* Contact */}
          <div>
            <p className="text-[var(--green-primary)] text-xs tracking-[0.2em] uppercase mb-5 font-medium">Contact</p>
            <div className="flex flex-col gap-4">
              <div className="flex items-start gap-3">
                <span className="text-[var(--green-primary)] text-sm mt-0.5">📍</span>
                <span className="text-[var(--text-muted)] text-sm">Avenue 123, Cotonou, Bénin</span>
              </div>
              <div className="flex items-center gap-3">
                <span className="text-[var(--green-primary)] text-sm">📞</span>
                <span className="text-[var(--text-muted)] text-sm">+229 01 57 00 00</span>
              </div>
              <div className="flex items-center gap-3">
                <span className="text-[var(--green-primary)] text-sm">📧</span>
                <span className="text-[var(--text-muted)] text-sm">contact@cleancar.bj</span>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom bar */}
        <div className="border-t border-[var(--border-subtle)] pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
          <p className="text-[var(--text-faint)] text-xs tracking-widest uppercase">
            © 2026 CleanCar Pro · Tous droits réservés
          </p>
          <div className="flex items-center gap-6">
            <p className="text-[var(--text-faint)]/50 text-xs tracking-widest uppercase">
              Cotonou, Bénin
            </p>
            <span className="w-1 h-1 rounded-full bg-[var(--border-subtle)]" />
          </div>
        </div>
      </div>
    </footer>
  );
}