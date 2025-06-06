import { useLocation } from 'react-router-dom';

import MessageBox from '@/components/MessageBox';
import ConfigureTwoFactorForm from '@/components/dashboard/forms/ConfigureTwoFactorForm';
import UpdateEmailAddressForm from '@/components/dashboard/forms/UpdateEmailAddressForm';
import UpdatePasswordForm from '@/components/dashboard/forms/UpdatePasswordForm';
import ContentBox from '@/components/elements/ContentBox';
import PageContentBlock from '@/components/elements/PageContentBlock';
import { useStoreState } from '@/state/hooks';

import Code from '../elements/Code';

export default () => {
    const { state } = useLocation();
    const user = useStoreState((state) => state.user.data);
    
    // Check if user is authenticated via OAuth (has external_id)
    const isOAuthUser = user?.externalId != null;

    return (
        <PageContentBlock title={'Your Settings'}>
            <h1 className='text-[52px] font-extrabold leading-[98%] tracking-[-0.14rem] mb-8'>Your Settings</h1>
            {state?.twoFactorRedirect && (
                <MessageBox title={'2-Factor Required'} type={'error'}>
                    Your account must have two-factor authentication enabled in order to continue.
                </MessageBox>
            )}

            <div className='flex flex-col w-full h-full gap-4'>
                <h2 className='mt-8 font-extrabold text-2xl'>Account Information</h2>
                {!isOAuthUser && (
                    <ContentBox title={'Email Address'} showFlashes={'account:email'}>
                        <UpdateEmailAddressForm />
                    </ContentBox>
                )}
                {isOAuthUser && (
                    <ContentBox title={'Email Address'}>
                        <p className='text-sm text-neutral-300 mb-4'>
                            Your email address is managed by your OAuth provider and cannot be changed here.
                        </p>
                        <div className='bg-neutral-800 rounded p-3'>
                            <p className='text-sm'>{user?.email}</p>
                        </div>
                    </ContentBox>
                )}
                <h2 className='mt-8 font-extrabold text-2xl'>Password and Authentication</h2>
                {!isOAuthUser && (
                    <ContentBox title={'Account Password'} showFlashes={'account:password'}>
                        <UpdatePasswordForm />
                    </ContentBox>
                )}
                {isOAuthUser && (
                    <ContentBox title={'Account Password'}>
                        <p className='text-sm text-neutral-300'>
                            Your password is managed by your OAuth provider. You cannot change it here.
                        </p>
                    </ContentBox>
                )}
                <ContentBox title={'Multi-Factor Authentication'}>
                    <ConfigureTwoFactorForm />
                </ContentBox>
                <h2 className='mt-8 font-extrabold text-2xl'>App</h2>
                <ContentBox title={'Panel Version'}>
                    <p className='text-sm mb-4'>
                        This is useful to provide Pyro staff if you run into an unexpected issue.
                    </p>
                    <div className='flex flex-col gap-4'>
                        <Code>{import.meta.env.VITE_PYRODACTYL_VERSION}</Code>
                        <Code>
                            Build {import.meta.env.VITE_PYRODACTYL_BUILD_NUMBER}, Commit{' '}
                            {import.meta.env.VITE_COMMIT_HASH.slice(0, 7)}
                        </Code>
                    </div>
                </ContentBox>
            </div>
        </PageContentBlock>
    );
};
