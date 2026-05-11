"""
Seed script: creates all DB tables and inserts the initial admin user.
Run with: python seed.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(__file__))

from app import create_app
from app.extensions import db
from app.models.user import User


def seed():
    app = create_app()
    with app.app_context():
        db.create_all()
        print("Database tables created.")

        existing = User.query.filter_by(email="admin@winnf.com").first()
        if existing:
            print("Admin user already exists. Skipping seed.")
            return

        admin = User(
            name="Admin WinNF",
            email="admin@winnf.com",
            role="admin",
            active=True,
        )
        admin.set_password("admin123")
        db.session.add(admin)
        db.session.commit()
        print("Admin user created:")
        print("  Email   : admin@winnf.com")
        print("  Password: admin123")
        print("  Role    : admin")


if __name__ == "__main__":
    seed()
