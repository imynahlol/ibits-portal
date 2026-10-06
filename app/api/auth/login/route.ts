import { NextResponse } from 'next/server';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { db } from '@/lib/db';

const JWT_SECRET = process.env.JWT_SECRET || 'fallback-secret';

export async function POST(request: Request) {
  try {
    const { email, password } = await request.json();

    // 1. find user
    const [users]: any = await db.query('SELECT * FROM users WHERE email = ?', [email]);
    if (users.length === 0) {
      return NextResponse.json({ error: 'Invalid credentials' }, { status: 401 });
    }

    const user = users[0];

    // 2. check password
    const isValid = await bcrypt.compare(password, user.password_hash);
    if (!isValid) {
      return NextResponse.json({ error: 'Invalid credentials' }, { status: 401 });
    }

    // 3. create JWT token
    const token = jwt.sign(
      { id: user.id, email: user.email, role: user.role, name: user.full_name },
      JWT_SECRET,
      { expiresIn: '1d' }
    );

    // 4. set cookie
    const response = NextResponse.json({ message: 'Login successful' });
    response.cookies.set('auth_token', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      maxAge: 60 * 60 * 24, // 1 day
      path: '/',
    });

    return response;
  } catch (error) {
    return NextResponse.json({ error: 'Something went wrong' }, { status: 500 });
  }
}