import { NextResponse } from 'next/server';
import bcrypt from 'bcryptjs';
import { db } from '@/lib/db';

export async function POST(request: Request) {
  try {
    const { email, password, full_name } = await request.json();

    // 1. Check if user already exists
    const [existing]: any = await db.query('SELECT * FROM users WHERE email = ?', [email]);
    if (existing.length > 0) {
      return NextResponse.json({ error: 'Email already exists' }, { status: 400 });
    }

    // 2. Hash the password
    const hashedPassword = await bcrypt.hash(password, 10);

    // 3. Insert into database
    await db.query(
      'INSERT INTO users (email, password_hash, full_name) VALUES (?, ?, ?)',
      [email, hashedPassword, full_name]
    );

    return NextResponse.json({ message: 'User created successfully' }, { status: 201 });
  } catch (error) {
    return NextResponse.json({ error: 'Something went wrong' }, { status: 500 });
  }
}