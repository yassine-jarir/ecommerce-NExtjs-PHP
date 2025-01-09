'use client';

import * as React from 'react';
import RouterLink from 'next/link';
import { useRouter } from 'next/navigation';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import Checkbox from '@mui/material/Checkbox';
import FormControl from '@mui/material/FormControl';
import FormControlLabel from '@mui/material/FormControlLabel';
import FormHelperText from '@mui/material/FormHelperText';
import InputLabel from '@mui/material/InputLabel';
import Link from '@mui/material/Link';
import OutlinedInput from '@mui/material/OutlinedInput';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import { Controller, useForm } from 'react-hook-form';
import { useAuth } from '@/contexts/AuthContexts';

type FormValues = {
  name: string;
  email: string;
  password: string;
  terms: boolean;
};

const defaultValues = { 
  name: '', 
  email: '', 
  password: '', 
  terms: false 
};

export function SignUpForm(): React.JSX.Element {
  const router = useRouter();
  const { signup } = useAuth();

  const [isPending, setIsPending] = React.useState<boolean>(false);
  const [errorMessage, setErrorMessage] = React.useState<string | null>(null);

  const { control, handleSubmit, formState: { errors } } = useForm<FormValues>({
    defaultValues,
  });

  const onSubmit = React.useCallback(
    async (values: FormValues): Promise<void> => {
      if (!values.terms) {
        setErrorMessage('You must accept the terms and conditions');
        return;
      }

      setIsPending(true);
      setErrorMessage(null);

      try {
        await signup(values.name, values.email, values.password);
        router.push('/auth/sign-in');
      } catch (error: any) {
        setErrorMessage(error.message);
      } finally {
        setIsPending(false);
      }
    },
    [router, signup]
  );

  return (
    <Stack spacing={3}>
      <Stack spacing={1}>
        <Typography variant="h4">Sign up</Typography>
        <Typography color="text.secondary" variant="body2">
          Already have an account?{' '}
          <Link component={RouterLink} href="/auth/sign-in" underline="hover" variant="subtitle2">
            Sign in
          </Link>
        </Typography>
      </Stack>
      <form onSubmit={handleSubmit(onSubmit)}>
        <Stack spacing={2}>
          <Controller
            control={control}
            name="name"
            rules={{
              required: 'Name is required',
              minLength: {
                value: 2,
                message: 'Name must be at least 2 characters',
              },
            }}
            render={({ field }) => (
              <FormControl error={Boolean(errors.name)}>
                <InputLabel>Full name</InputLabel>
                <OutlinedInput {...field} label="Full name" />
                {errors.name && <FormHelperText>{errors.name.message}</FormHelperText>}
              </FormControl>
            )}
          />
          
          <Controller
            control={control}
            name="email"
            rules={{
              required: 'Email is required',
              pattern: {
                value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                message: 'Invalid email address',
              },
            }}
            render={({ field }) => (
              <FormControl error={Boolean(errors.email)}>
                <InputLabel>Email address</InputLabel>
                <OutlinedInput {...field} label="Email address" type="email" />
                {errors.email && <FormHelperText>{errors.email.message}</FormHelperText>}
              </FormControl>
            )}
          />
          <Controller
            control={control}
            name="password"
            rules={{
              required: 'Password is required',
              minLength: {
                value: 6,
                message: 'Password must be at least 6 characters',
              },
            }}
            render={({ field }) => (
              <FormControl error={Boolean(errors.password)}>
                <InputLabel>Password</InputLabel>
                <OutlinedInput {...field} label="Password" type="password" />
                {errors.password && <FormHelperText>{errors.password.message}</FormHelperText>}
              </FormControl>
            )}
          />
          <Controller
            control={control}
            name="terms"
            rules={{ required: 'You must accept the terms and conditions' }}
            render={({ field }) => (
              <FormControlLabel
                control={<Checkbox {...field} />}
                label={
                  <React.Fragment>
                    I have read the{' '}
                    <Link component="span" variant="subtitle2">
                      terms and conditions
                    </Link>
                  </React.Fragment>
                }
              />
            )}
          />
          {errorMessage && <Alert color="error">{errorMessage}</Alert>}
          <Button disabled={isPending} type="submit" variant="contained">
            Sign up
          </Button>
        </Stack>
      </form>
    </Stack>
  );
}
